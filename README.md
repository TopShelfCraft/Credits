# Credits

_A simple starter framework for implementing credits/accounts with Craft Commerce._

**A [Top Shelf Craft](https://topshelfcraft.com) creation**  
[Michael Rog](https://michaelrog.com), Proprietor 


* * *



### Interacting with Credits accounts via command line

The _Credits_ plugin provides commands for an admin to manually check or manipulate the balance of a credits account.

To check a balance:

```
./craft credits/accounts/check-balance --user=767

./craft credits/accounts/check-balance --accountId=1

./craft credits/accounts/check-balance --reference=b8d0e46d40767228493d84283fe5ac1e
```

To credit/debit/adjust an account:

```
./craft credits/accounts/credit --user=767 --amount=100

./craft credits/accounts/debit --accountId=767 --amount=100

./craft credits/accounts/adjust --reference=b8d0e46d40767228493d84283fe5ac1e --amount=-100
```

(As a convention, the amounts of Credits and Debits will always be positive. The amount of an Adjustment may be positive or negative.)

These adjustment actions may also include an optional note:

```
./craft credits/accounts/credit --user=767 --amount=100 --note="Here, have a hundo."

./craft credits/accounts/adjust --user=767 --amount=-100 --note="Oops, need that hundo back."

./craft credits/accounts/debit --user=767 --amount=100 --note="Something offline cost a hundo."
```

Note: The system will not object if an admin debits an account to a balance below zero.
