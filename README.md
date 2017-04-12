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

## building README file

When contract is changed `README` file should be updated.  
I've created a simple PHP build tool which will use README template and pass to it information about compiled contract.

To use it, simply, run `make docs`. This command requires `docker` and `docker-compose` to be installed.

If you don't want to use `docker` you can compile README with this commands:

```
solc --combined-json "abi,bin" zeppelin/SafeMath.sol=/usr/src/app/installed_contracts/zeppelin/contracts/SafeMath.sol --optimize contracts/EthSplit.sol > build/solc.json

php docs/build.php > README.md
```

Note: you will need to install `solc` compiler and `PHP` (>= 7.0).

# contract
## interface

### pretty version:

```json
[
    {
        "constant": false,
        "inputs": [],
        "name": "payout",
        "outputs": [
            {
                "name": "result",
                "type": "bool"
            }
        ],
        "payable": false,
        "type": "function"
    },
    {
        "inputs": [
            {
                "name": "shareholdersList",
                "type": "address[]"
            },
            {
                "name": "donateTo",
                "type": "address"
            }
        ],
        "payable": false,
        "type": "constructor"
    },
    {
        "payable": true,
        "type": "fallback"
    }
]
```

### minified:

```json
[{"constant":false,"inputs":[],"name":"payout","outputs":[{"name":"result","type":"bool"}],"payable":false,"type":"function"},{"inputs":[{"name":"shareholdersList","type":"address[]"},{"name":"donateTo","type":"address"}],"payable":false,"type":"constructor"},{"payable":true,"type":"fallback"}]
```

## compiled data

Contract was compiled by stable version of `solc` compiler. It was optimized using default values.

```
6060604052341561000c57fe5b6040516102e13803806102e183398101604052805160208201519101905b815161003d906000906020850190610061565b5060018054600160a060020a031916600160a060020a0383161790555b50506100f2565b8280548282559060005260206000209081019282156100b6579160200282015b828111156100b65782518254600160a060020a031916600160a060020a03909116178255602090920191600190910190610081565b5b506100c39291506100c7565b5090565b6100ef91905b808211156100c3578054600160a060020a03191681556001016100cd565b5090565b90565b6101e0806101016000396000f300606060405236156100255763ffffffff60e060020a60003504166363bd1d4a811461002e575b61002c5b5b565b005b341561003657fe5b61003e610052565b604080519115158252519081900360200190f35b6000600160a060020a033016318180808315156100725760009450610159565b61008830600160a060020a0316316103e8610160565b600154909350600160a060020a0316158015906100ca5750600154604051600160a060020a039091169084156108fc029085906000818181858888f193505050505b156100d55782840393505b6000546100e3908590610160565b9150600090505b60005481101561015457600080548290811061010257fe5b906000526020600020900160005b90546040516101009290920a9004600160a060020a0316906108fc8415029084906000818181858888f19350505050151561014b5760006000fd5b5b6001016100ea565b600194505b5050505090565b60006000610170600084116101a3565b828481151561017b57fe5b049050610198838581151561018c57fe5b068285020185146101a3565b8091505b5092915050565b8015156101b05760006000fd5b5b505600a165627a7a723058204a6bb82f31f0037b61714b9cc89d6209d7aaf19401b9ae01bfb7a4152a586ac40029
```

## deployment

### web3

```js
const shareholdersList = [
    // array of ETH wallet address
    // eg. 0x0 :)
];
const donateTo = '0x0'; // don't donate
const contract = web3.eth.contract([{"constant":false,"inputs":[],"name":"payout","outputs":[{"name":"result","type":"bool"}],"payable":false,"type":"function"},{"inputs":[{"name":"shareholdersList","type":"address[]"},{"name":"donateTo","type":"address"}],"payable":false,"type":"constructor"},{"payable":true,"type":"fallback"}]);

contract.new(shareholdersList, donateTo, {
    from: web3.eth.accounts[0],
    gas: '4700000',
    data: '0x6060604052341561000c57fe5b6040516102e13803806102e183398101604052805160208201519101905b815161003d906000906020850190610061565b5060018054600160a060020a031916600160a060020a0383161790555b50506100f2565b8280548282559060005260206000209081019282156100b6579160200282015b828111156100b65782518254600160a060020a031916600160a060020a03909116178255602090920191600190910190610081565b5b506100c39291506100c7565b5090565b6100ef91905b808211156100c3578054600160a060020a03191681556001016100cd565b5090565b90565b6101e0806101016000396000f300606060405236156100255763ffffffff60e060020a60003504166363bd1d4a811461002e575b61002c5b5b565b005b341561003657fe5b61003e610052565b604080519115158252519081900360200190f35b6000600160a060020a033016318180808315156100725760009450610159565b61008830600160a060020a0316316103e8610160565b600154909350600160a060020a0316158015906100ca5750600154604051600160a060020a039091169084156108fc029085906000818181858888f193505050505b156100d55782840393505b6000546100e3908590610160565b9150600090505b60005481101561015457600080548290811061010257fe5b906000526020600020900160005b90546040516101009290920a9004600160a060020a0316906108fc8415029084906000818181858888f19350505050151561014b5760006000fd5b5b6001016100ea565b600194505b5050505090565b60006000610170600084116101a3565b828481151561017b57fe5b049050610198838581151561018c57fe5b068285020185146101a3565b8091505b5092915050565b8015156101b05760006000fd5b5b505600a165627a7a723058204a6bb82f31f0037b61714b9cc89d6209d7aaf19401b9ae01bfb7a4152a586ac40029'
}, (e, contract) => {
    console.log(e, contract);
    if (contract && typeof contract.address !== 'undefined') {
         console.log('Contract mined! address: ' + contract.address + ' transactionHash: ' + contract.transactionHash);
    }
});
```

# usage

Deployed contract is able to do two things:

1. receive ether transfers
2. payout stored ether to defined shareholders (via `payout()` method)

## selfdestruct

Currently contract does not support selfdestructing.

## security considerations

This contract is not secured in any way. It doesn't have to. 

* shareholders list can't be changed
* triggering `payout()` will give no benefit for someone who is not defined in shareholders list

# donate

If you're happy with my work feel free to donate me with some ether (`0x4a03ec5e48de60048d8fd5e004b443db51d9f0f5`) :)

## automatic donation

If you're **very** happy with my work you can set `donateTo` constructor argument during contract deployment.  
Every time you make a payout I'll receive 0.1% of that transfer.

```js
const shareholdersList = [
    // array of ETH wallet address
    // eg. 0x0 :)
];
const donateTo = '0x4a03ec5e48de60048d8fd5e004b443db51d9f0f5';
const contract = web3.eth.contract([{"constant":false,"inputs":[],"name":"payout","outputs":[{"name":"result","type":"bool"}],"payable":false,"type":"function"},{"inputs":[{"name":"shareholdersList","type":"address[]"},{"name":"donateTo","type":"address"}],"payable":false,"type":"constructor"},{"payable":true,"type":"fallback"}]);

contract.new(shareholdersList, donateTo, {
    from: web3.eth.accounts[0],
    gas: '4700000',
    data: '0x6060604052341561000c57fe5b6040516102e13803806102e183398101604052805160208201519101905b815161003d906000906020850190610061565b5060018054600160a060020a031916600160a060020a0383161790555b50506100f2565b8280548282559060005260206000209081019282156100b6579160200282015b828111156100b65782518254600160a060020a031916600160a060020a03909116178255602090920191600190910190610081565b5b506100c39291506100c7565b5090565b6100ef91905b808211156100c3578054600160a060020a03191681556001016100cd565b5090565b90565b6101e0806101016000396000f300606060405236156100255763ffffffff60e060020a60003504166363bd1d4a811461002e575b61002c5b5b565b005b341561003657fe5b61003e610052565b604080519115158252519081900360200190f35b6000600160a060020a033016318180808315156100725760009450610159565b61008830600160a060020a0316316103e8610160565b600154909350600160a060020a0316158015906100ca5750600154604051600160a060020a039091169084156108fc029085906000818181858888f193505050505b156100d55782840393505b6000546100e3908590610160565b9150600090505b60005481101561015457600080548290811061010257fe5b906000526020600020900160005b90546040516101009290920a9004600160a060020a0316906108fc8415029084906000818181858888f19350505050151561014b5760006000fd5b5b6001016100ea565b600194505b5050505090565b60006000610170600084116101a3565b828481151561017b57fe5b049050610198838581151561018c57fe5b068285020185146101a3565b8091505b5092915050565b8015156101b05760006000fd5b5b505600a165627a7a723058204a6bb82f31f0037b61714b9cc89d6209d7aaf19401b9ae01bfb7a4152a586ac40029'
}, (e, contract) => {
    console.log(e, contract);
    if (contract && typeof contract.address !== 'undefined') {
         console.log('Contract mined! address: ' + contract.address + ' transactionHash: ' + contract.transactionHash);
    }
});
```
