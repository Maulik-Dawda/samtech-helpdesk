<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('contextmenu', function (e) {
    e.preventDefault();
});

document.addEventListener('keydown', function (e) {
    if (
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && ['I', 'i', 'J', 'j', 'C', 'c'].includes(e.key)) ||
        (e.ctrlKey && ['U', 'u', 'S', 's'].includes(e.key))
    ) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
});
</script>
<?php require_once ROOT_PATH . "/app/Views/layouts/loader.php"; ?>
</body>
</html>