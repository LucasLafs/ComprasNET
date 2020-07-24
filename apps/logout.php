<?php
session_start();
session_destroy();
echo "Sessão destruída!";
header("Location: login.php");