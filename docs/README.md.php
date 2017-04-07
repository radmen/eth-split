# eth-split

Semi-automatic contract which splits received ether equally to declared shareholders.

# foreword

I was asked to create a contract which would automatically split received ether from mining pools to declared shareholders.

Mining pools tend do set low gas limit for transactions so it turned out that this contract should only receive transfer. Payout to shareholders has to be triggered manually (using `payout()` method).

# development

## required software:

* [truffle](https://github.com/trufflesuite/truffle) 
* [TestRPC](https://github.com/ethereumjs/testrpc)
* *(optional)* [Docker](https://github.com/docker/docker) (with `docker-compose`) - for convenience, I'm using dedicated images

## installing dependencies

```
docker-compose run --rm truffle install
```

## running tests

```
docker-compose up -d testrpc
docker-compose run --rm truffle test
```

After longer development (and test runs) it may turn out that `coinbase` set in TestRPC may run out of ether which will fail tests. If this happens TestRPC server has to be restarted.

```
docker-compose restart testrpc
```

# contract
## interface

### pretty version:

```json
<?php echo $abiPretty.PHP_EOL; ?>
```

### minified:

```json
<?php echo $abi.PHP_EOL; ?>
```

## compiled data

Contract was compiled by stable version of `solc` compiler. It was optimized using default values.

```
<?php echo $bin.PHP_EOL; ?>
```

## deployment

### web3

```js
const shareholdersList = [
    // array of ETH wallet address
    // eg. 0x0 :)
];
const donateTo = '0x0'; // don't donate
const contract = web3.eth.contract(<?php echo $abi; ?>);

contract.new(shareholdersList, donateTo, {
    from: web3.eth.accounts[0],
    gas: '4700000',
    data '0x<?php echo $bin; ?>'
}, (e, contract) => {
    console.log(e, contract);
    if (typeof contract.address !== 'undefined') {
         console.log('Contract mined! address: ' + contract.address + ' transactionHash: ' + contract.transactionHash);
    }
});
```

# donate

If you feel an urge to share some ether with me you can do it by setting `donateTo` contructor argument.  
Simply, pass, address to my wallet (`<?php echo $donateAddress; ?>`). Every time you make a payout I'll receive 0,1% of that transfer.

```js
const shareholdersList = [
    // array of ETH wallet address
    // eg. 0x0 :)
];
const donateTo = '<?php echo $donateAddress; ?>';
const contract = web3.eth.contract(<?php echo $abi; ?>);

contract.new(shareholdersList, donateTo, {
    from: web3.eth.accounts[0],
    gas: '4700000',
    data '0x<?php echo $bin; ?>'
}, (e, contract) => {
    console.log(e, contract);
    if (typeof contract.address !== 'undefined') {
         console.log('Contract mined! address: ' + contract.address + ' transactionHash: ' + contract.transactionHash);
    }
});
```
