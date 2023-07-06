<?php

echo "<p>webhook start</p>";
exec('cd /home/masahiro111/laradoc/', $op);
print_r($op);
exec('git pull');

exec('cd /home/masahiro111/laradoc/resources/docs/', $op);
print_r($op);
exec('git pull');

echo "<p>webhook finish</p>";
