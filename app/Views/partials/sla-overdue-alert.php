<?php
$overdueTickets = is_array($overdueTickets ?? null) ? $overdueTickets : [];
$overdueCount = count($overdueTickets);
$isCustomerUser = ($_SESSION['auth_user_role'] ?? '') === 'user';
$ticketShowBase = $isCustomerUser ? (BASE_URL . '/tickets/show/') : (BASE_URL . '/agent/tickets/show/');
$ticketIndexBase = $isCustomerUser ? (BASE_URL . '/tickets') : (BASE_URL . '/agent/tickets');
?>

<?php if ($overdueCount > 0): ?>

    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-4" style="background: #fef2f2; border-left: 5px solid #ef4444 !important;">

        <div class="d-flex align-items-start gap-3 flex-wrap flex-md-nowrap">

            <div class="flex-shrink-0 text-danger fs-3">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>

            <div class="flex-grow-1 w-100">

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">

                    <h5 class="fw-bold text-danger mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        SLA Overdue Alert: <?= $overdueCount; ?> Ticket<?= $overdueCount === 1 ? '' : 's'; ?> Exceeded SLA Timeframe
                    </h5>

                    <span class="badge bg-danger text-white rounded-pill px-3 py-2">
                        <i class="bi bi-exclamation-octagon-fill me-1"></i> Immediate Attention Required
                    </span>

                </div>

                <p class="text-secondary small mb-3">
                    The following unresolved support tickets have exceeded the resolution SLA limits (<strong>Urgent: 1 day, High: 3 days, Medium: 5 days, Low: 7 days</strong>):
                </p>

                <div class="table-responsive bg-white rounded-3 p-2 border">

                    <table class="table table-hover align-middle mb-0 small">

                        <thead class="table-light">

                            <tr>
                                <th>Ticket No</th>
                                <th>Subject</th>
                                <th>Customer / Org</th>
                                <th>Assigned Agent</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Open Duration</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach (array_slice($overdueTickets, 0, 5) as $ot): ?>

                                <?php
                                $p = strtolower($ot['priority'] ?? 'medium');

                                $pBadgeStyle = match($p) {
                                    'urgent' => 'background:#fee2e2; color:#b91c1c;',
                                    'high' => 'background:#fef3c7; color:#92400e;',
                                    'medium' => 'background:#dbeafe; color:#1d4ed8;',
                                    'low' => 'background:#f3f4f6; color:#374151;',
                                    default => 'background:#dbeafe; color:#1d4ed8;'
                                };

                                $slaLimitDays = match($p) {
                                    'urgent' => 1,
                                    'high' => 3,
                                    'medium' => 5,
                                    'low' => 7,
                                    default => 7
                                };

                                $daysOpen = (int)($ot['days_open'] ?? 0);
                                ?>

                                <tr>

                                    <td class="fw-bold">
                                        <a href="<?= $ticketShowBase . (int)$ot['id']; ?>" class="text-decoration-none text-danger">
                                            <?= htmlspecialchars($ot['ticket_no'] ?? '-'); ?>
                                        </a>
                                    </td>

                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <?= htmlspecialchars(mb_strimwidth($ot['subject'] ?? 'Untitled', 0, 40, '...')); ?>
                                        </div>
                                    </td>

                                    <td>
                                        <div>
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($ot['customer_name'] ?? $ot['organization_name'] ?? 'N/A'); ?>
                                            </div>

                                            <?php if (!empty($ot['organization_name'])): ?>
                                                <div class="text-muted small">
                                                    <?= htmlspecialchars($ot['organization_name']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if (!empty($ot['assigned_agent_name'])): ?>
                                            <span class="badge" style="background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:8px; font-size:11px; font-weight:600;">
                                                <i class="bi bi-person-badge me-1"></i>
                                                <?= htmlspecialchars($ot['assigned_agent_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">Unassigned</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <span class="badge" style="<?= $pBadgeStyle; ?> padding:5px 10px; border-radius:8px; font-size:11px; font-weight:600;">
                                            <?= ucfirst($p); ?> (Max <?= $slaLimitDays; ?>d)
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border" style="padding:5px 10px; border-radius:8px; font-size:11px;">
                                            <?= ucfirst(str_replace('_', ' ', $ot['status'] ?? 'Open')); ?>
                                        </span>
                                    </td>

                                    <td class="fw-bold text-danger">
                                        <i class="bi bi-hourglass-bottom me-1"></i>
                                        <?= $daysOpen; ?> <?= $daysOpen === 1 ? 'Day' : 'Days'; ?> Open
                                    </td>

                                    <td class="text-end">
                                        <a href="<?= $ticketShowBase . (int)$ot['id']; ?>" class="btn btn-sm btn-danger px-3 py-1">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> View
                                        </a>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <?php if ($overdueCount > 5): ?>

                    <div class="mt-2 text-end">
                        <a href="<?= $ticketIndexBase; ?>" class="small fw-bold text-danger text-decoration-none">
                            View All <?= $overdueCount; ?> Overdue Tickets &rarr;
                        </a>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

<?php endif; ?>
