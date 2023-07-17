<?php

// laradoc
echo "<p>webhook start</p>";
exec('cd /home/masahiro111/laradoc/', $output, $result);
print_r($output . $result);
// exec('git pull', $output, $result);
// print_r($result);
exec('git fetch origin main', $output, $result);
print_r($output . $result);

exec('git reset --hard origin/main', $output, $result);
print_r($output . $result);


// document
exec('cd /home/masahiro111/laradoc/resources/docs/', $output, $result);
print_r($output . $result);

// print_r($result);
// exec('git pull', $output, $result);
// print_r($result);
exec('git fetch origin main', $output, $result);
print_r($output . $result);

exec('git reset --hard origin/main', $output, $result);
print_r($output . $result);

echo "<p>webhook finish</p>";
