<?php

return [
  'DB_USER' => $_ENV['DB_USER'] ?? '',
  'DB_MDP' => $_ENV['DB_MDP'] ?? '',
  'DB_NAME' => $_ENV['DB_NAME'] ?? '',
  'DB_SGBD' => $_ENV['DB_SGBD'] ?? '',

  # exemple host : localhost, localhost:3306 , test.com, https://exemple.com
  'DB_HOST' => $_ENV['DB_HOST'] ?? ''
];
