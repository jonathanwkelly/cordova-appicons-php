#### Overview
This PHP script can be used to generate app icons for iOS and Android builds. It was written while operating in the context of a <a href="https://cordova.apache.org/" target="_blank">Cordova</a> project. 

#### Dependencies
<a href="http://www.imagemagick.org/script/install-source.php" target="_blank">Imagemagick</a>

#### Setup
You'll need to have a source image that is larger than the maximum size you'll be generating.

*YAML Config*
Included in this repo is `Spyc.php` to parse the YAML config file. (You may have the PECL YAML package installed on your machine, but either way, there should be no conflict.) Make your own `config.yaml` file and include the following items:

`debug=true|false`
Used to output some debug info while the script is running.

`xml=true|false`
If you want the icon-related tags needed for the <a href="https://cordova.apache.org/docs/en/5.1.1/config_ref/index.html" target="_blank">config.xml</a> to be output at the end of the script, set this to true.

`icons`
See `.config.yaml.dist` to see examples of the conventions used in this array. One caveat here: the `files` array is keyed with the _basename_ of the resulting image to be generated, but can also include a relative path. If included, this path segment will be apended to the 'base' path.

#### How To Run It
The script is meant to be run from the command-line  
```
$ cd /path/to/cordova-appicons/php
$ php ./generate.php
```