<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = $_SESSION['message'] ?? null;

if ($message): ?>
    <div id="notification" class="notification is-<?= htmlspecialchars($message['type']) ?>" style="display: block;">
        <button class="delete" onclick="hideNotification()"></button>
        <span id="notification-message"><?= htmlspecialchars($message['content']) ?></span>
    </div>
    <?php
    unset($_SESSION['message']);
endif; ?>

<script>
    function hideNotification() {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const notification = document.getElementById('notification');
        if (notification) {
            setTimeout(hideNotification, 2000);
        }
    });
</script>
