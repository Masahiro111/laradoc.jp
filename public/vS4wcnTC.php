<?php

// laradoc
echo "<p>webhook start</p>";
exec('cd /home/masahiro111/laradoc/', $output, $result);
exec('git pull', $output, $result);
print_r($result);
// exec('git fetch origin/main');
// exec('git reset --hard origin/main');

// document
exec('cd /home/masahiro111/laradoc/resources/docs/', $output, $result);
exec('git pull', $output, $result);
print_r($result);
// exec('git fetch origin/main');
// exec('git reset --hard origin/main');

echo "<p>webhook finish</p>";
