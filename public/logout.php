<?php
session_start();
session_destroy();
header('Location: /webshoppen/public/');
exit();
?> 