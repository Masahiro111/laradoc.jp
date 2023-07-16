<?php

// laradoc
echo "<p>webhook start</p>";
exec('cd /home/masahiro111/laradoc/', $op);
print_r($op);
exec('git fetch origin/main');
exec('git reset --hard origin/main');

// document
exec('cd /home/masahiro111/laradoc/resources/docs/', $op);
print_r($op);
exec('git fetch origin/main');
exec('git reset --hard origin/main');

echo "<p>webhook finish</p>";
