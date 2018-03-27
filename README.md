# script-style-push
TYPO3 Extension to push javascript and css files over a http/2 connection.
The css and javascript files are automatically parsed from rendered html.

This extension does not push images autoamtically, because of huge traffic overload when 
using responsive images. Each image in the pciture source would be pushed 
to the client.

To add other resources as push, TypoScript can be used. 

Example

```
plugin.tx_scriptstylepush {
	settings {
		headers {
			0 = EXT:my_ext/Resources/Public/img/my-image.jpeg
			1 = EXT:my_ext/Resources/Public/fonts/my-font.woff2
		}
	}
}
```

To make other domains work for push, you have to specify them with (without protocol)

```
plugin.tx_scriptstylepush {
	settings {
		domains {
			0 = www.domain.tld
			1 = domain.tld
		}
	}
}
```