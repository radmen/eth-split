<?php
$templateFile = __DIR__.'/README.md.php';
$jsonFile = __DIR__.'/../build/solc.json';

$compile = function (array $with) use ($templateFile) {
    extract($with);
    ob_start();
    include($templateFile);
    return ob_get_clean();
};

if (false === file_exists($jsonFile)) {
    echo "Compiled solc file missing\n";
    exit(1);
}

$data = json_decode(file_get_contents($jsonFile), true);
$contractData = $data['contracts']['contracts/EthSplit.sol:EthSplit'];

$contractData = array_merge(
    $data['contracts']['contracts/EthSplit.sol:EthSplit'],
    [
        'abiPretty' => json_encode(
            json_decode($data['contracts']['contracts/EthSplit.sol:EthSplit']['abi']),
            JSON_PRETTY_PRINT
        ),
        'donateAddress' => 'WIP: 0x0',
    ]
);

echo $compile($contractData);
