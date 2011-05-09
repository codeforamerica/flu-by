This directory is meant for scripts that have manipulated the data.

## Resources Used

1. [Sag](http://www.saggingcouch.com/)
2. [GeoPHP](https://github.com/phayes/geoPHP)
3. [Krumo](http://krumo.sourceforge.net/)
4. [SimpleGeo PHP PEAR Library]()
5. Google Geocoding Services (some inspiration from [here](http://eileenaw.wordpress.com/2010/07/18/google-map-geocoding-using-php-codes-for-wordpress/))

## PEAR on Mac

For some geocoding services from SimpleGeo and PHP, it is easier
to get PEAR installed.  Here are some basics steps for Mac, which
assume you just want it for your user and are using MAMP.  Also 
note that MAMP comes with a version of PEAR, and that if you want
to use that one, the first couple steps are not needed:

1. `wget http://pear.php.net/go-pear.phar`
2. `php -d detect_unicode=0 go-pear.phar`
    * You could change where it is installed here
3. I would suggest aliasing the pear path in your .bash_profile
   * `alias pear='/path/to/pear/bin/pear'`
4. Check it is working: `pear config-show`
   * You should get a list of configs
5. Update php.ini in MAMP to include new directory.  This will probably be here:
   * `/Applications/MAMP/conf/php5.3/php.ini`
   * Add the following to the list of included paths: `include_path = "<WHAT IS ALREADY THERE>:/path/to/pear/share/pear"`
       