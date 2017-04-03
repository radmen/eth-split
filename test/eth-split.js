const EthSplit = artifacts.require('./EthSplit.sol');

const resolveCallback = (resolve, reject) => (err, result) => {
    if (err) {
        reject(err);
    } else {
        resolve(result);
    }
};

const toPromise = (callback, ...args) => new Promise((resolve, reject) => callback(...args, resolveCallback(resolve, reject)));

const createPayoutTest = (owner, list) => () => {
    const startBalance = web3.toWei(1, 'ether');
    const payoutAmount = startBalance / list.length;
    const expectedBalance = list.map(address => {
        return web3.eth.getBalance(address).add(payoutAmount).toNumber();
    });

    return EthSplit.new(list, '0x0')
        .then(instance => {
            return toPromise(web3.eth.sendTransaction, {
                from: owner,
                to: instance.address,
                value: startBalance,
            })
                .then(() => instance.payout({ from: owner }))
                .then(() => {
                    const currentBalance = list.map(address => {
                        return web3.eth.getBalance(address).toNumber();
                    })

                    assert.deepEqual(currentBalance, expectedBalance);
                });
        });
};

contract('EthSplit', accounts => {
    const [owner, jon, arrya, rob, donation] = accounts;

    it('accepts money', () => {
        return EthSplit.new()
            .then(instance => {
                return toPromise(web3.eth.sendTransaction, {
                    from: owner,
                    to: instance.address,
                    value: web3.toWei(1, 'ether'),
                })
                    .then(transId => web3.eth.getBalance(instance.address))
                    .then(balance => balance.toNumber())
                    .then(balance => assert.equal(balance, web3.toWei(1, 'ether')));
            });
    });

    it('splits eth between two shareholders', createPayoutTest(owner, [jon, arrya]));

    it('splits eth between four shareholders', createPayoutTest(owner, [jon, arrya, rob, donation]));

    it('supports donations', () => {
        const list = [jon, arrya];
        const startBalance = web3.toWei(1, 'ether');
        const donationValue = startBalance / 1000;
        const expectedDonationBalance = web3.eth.getBalance(donation).add(donationValue).toNumber();
        const expectedBalance = list.map(address => {
            return web3.eth.getBalance(address)
                .add((startBalance - donationValue) / list.length)
                .toNumber();
        });

        return EthSplit.new([jon, arrya], donation)
            .then(instance => {
                return toPromise(web3.eth.sendTransaction, {
                    from: owner,
                    to: instance.address,
                    value: startBalance,
                })
                .then(() => instance.payout())
                .then(() => {
                    const currentBalance = list.map(address => web3.eth.getBalance(address).toNumber());
                    assert.deepEqual(currentBalance, expectedBalance);

                    const donationBalance = web3.eth.getBalance(donation).toNumber();
                    assert.equal(donationBalance, expectedDonationBalance);
                })
            });
    });
});
