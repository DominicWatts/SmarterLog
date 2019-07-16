# Magento 2 Smarter Log #

Log with archive and rotation.

## Install instructions ##

`composer require dominicwatts/smarterlog`

`php bin/magento setup:upgrade`

## Usage instructions ##

Trigger via shell script

`xigen:smarterlog:rotate <rotate>`

`php bin/magento xigen:smarterlog:rotate rotate`

Or let magento cron handle process

Default schedule is `0 0 * * * `

(At 00:00)

Can be disabled via Store > Configuration > Xigen > Smarter Log