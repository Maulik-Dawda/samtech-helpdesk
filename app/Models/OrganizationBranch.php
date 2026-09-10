<?php

require_once ROOT_PATH . "/app/Core/Model.php";

class OrganizationBranch extends Model
{
    public function getByOrganizationId($organizationId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM organization_branches
            WHERE organization_id = ?
            ORDER BY name ASC
        ");

        $stmt->execute([(int)$organizationId]);

        return $stmt->fetchAll();
    }

    public function getActiveByOrganizationId($organizationId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM organization_branches
            WHERE organization_id = ?
            AND is_active = 1
            ORDER BY name ASC
        ");

        $stmt->execute([(int)$organizationId]);

        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM organization_branches
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([(int)$id]);

        return $stmt->fetch();
    }

    public function syncBranches($organizationId, array $branchNames)
    {
        $organizationId = (int)$organizationId;

        // Clean array of trimmed non-empty branch names
        $validBranches = [];
        foreach ($branchNames as $item) {
            $name = is_array($item) ? trim($item['name'] ?? '') : trim($item);
            if ($name !== '') {
                $validBranches[] = $name;
            }
        }

        $existing = $this->getByOrganizationId($organizationId);
        $existingMap = [];
        foreach ($existing as $b) {
            $existingMap[mb_strtolower(trim($b['name']))] = $b;
        }

        $processedIds = [];

        foreach ($validBranches as $name) {
            $lower = mb_strtolower($name);

            if (isset($existingMap[$lower])) {
                // Update existing branch name & ensure active
                $branchId = (int)$existingMap[$lower]['id'];
                $stmt = $this->db->prepare("
                    UPDATE organization_branches
                    SET name = ?, is_active = 1
                    WHERE id = ?
                ");
                $stmt->execute([$name, $branchId]);
                $processedIds[] = $branchId;
            } else {
                // Insert new branch
                $stmt = $this->db->prepare("
                    INSERT INTO organization_branches
                    (organization_id, name, is_active)
                    VALUES (?, ?, 1)
                ");
                $stmt->execute([$organizationId, $name]);
                $processedIds[] = (int)$this->db->lastInsertId();
            }
        }

        // Delete branches that are no longer present
        if (!empty($existing)) {
            foreach ($existing as $b) {
                if (!in_array((int)$b['id'], $processedIds, true)) {
                    $stmt = $this->db->prepare("DELETE FROM organization_branches WHERE id = ?");
                    $stmt->execute([(int)$b['id']]);
                }
            }
        }

        return true;
    }
}
