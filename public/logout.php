<?php
require_once '../includes/functions.php';

logout();

setFlashMessage('Du har loggats ut!', 'success');
header('Location: /webshoppen/public/');
exit();
?> 