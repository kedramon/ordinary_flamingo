### Installation

Clone the repo:
``` bash
git clone git@github.com:kedramon/ordinary_flamingo.git
```

```bash
cd ordinary_flamingo
composer install
```

Run the test:
```bash
php bin/phpunit
```

Run the command to make calculations from import.txt
```bash
php bin/console app:process-file import/input.txt
```

## Remarks

As for an input file we have it in specific format, I would implement a processor which will be chosen by a 
factory class depending on input format, but this requires extra param and that is not in a scope for this task.

In the list of country codes there is a mistake, 'PO' should probably be 'PL' for Poland, 'PO' does not exist. 

The service to provide BIN was limited to 5 requests per hour, so by default a working one is used, to change it back
replace it in service.yaml
```yaml
    App\Service\CalculationService:
        arguments:
            - '@App\Service\BinProvider\HandyBinProvider'

```
