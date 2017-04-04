# eth-split

Semi-automatic contract which splits received ETH equally to declared shareholders.

# foreword

I was asked to create a contract which would automatically split received ETH from mining pools to declared shareholders.

Mining pools tend do set low gas limit during ether payout so it turned out that this contract should only receive transfer. Payout to shareholders has to be triggered manually (using `payout()` method).

# development

Required software:

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

# contract deployment

@TODO

# donate

@TODO