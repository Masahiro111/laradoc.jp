<?php

// laradoc
echo "<p>webhook start</p>";
exec('cd /home/masahiro111/laradoc/', $output, $result);
var_dump($output);
print_r($result);

// exec('git pull', $output, $result);
// print_r($result);
exec('git fetch origin main', $output, $result);
var_dump($output);
print_r($result);

exec('git reset --hard origin/main', $output, $result);
var_dump($output);
print_r($result);


// document
exec('cd /home/masahiro111/laradoc/resources/docs/ && git fetch origin main && git reset --hard origin/main', $output, $result);
var_dump($output);
print_r($result);

// print_r($result);
// exec('git pull', $output, $result);
// print_r($result);
// exec('git fetch origin main', $output, $result);
// var_dump($output);
// print_r($result);

// exec('git reset --hard origin/main', $output, $result);
// var_dump($output);
// print_r($result);

echo "<p>webhook finish</p>";
