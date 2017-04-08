# script-style-push
TYPO3 Extension to push javascript and css files over a http/2 connection. 

This extension does not push images, because of huge traffic overload when 
using responsive images. Each image in the pciture source would be pushed 
to the client.

To add other resources as push, TypoScript can be used `config.additionalHeaders`
