# HTTP/2 Push Extension for TYPO3

TYPO3 Extension to push JavaScript and CSS files over a HTTP/2 connection.
The CSS and JavaScript files are automatically parsed from rendered html.

This extension does not push images automatically, because of huge traffic overload when 
using responsive images. Each image in the picture source would be pushed 
to the client.


## Installation

`composer require codemonkey1988/script-style-push`

## Configuration

To support your used CSS and JavaScript files, you just need to install this extension.
The extension will automatically push every CSS and JavaScript file found in the 
html response.

You can add custom resources to push. Just add a comma separated list of asset paths 
to the site configuration. Just add a comma separated list of assets. 
Keep in mind, that this might change in the future as TYPO3 supports additional custom
field types for site configuration.

### Disabling this extension

If you need to disable generating the Link header, 
you can do this by setting the env variable `SCRIPT_STYLE_PUSH_ENABLED` to 1.
