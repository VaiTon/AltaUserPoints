files = LICENSE \
	manifest.xml \
	script.php \
	site/ \
	admin/ \
	modules/ \
	languages-admin/ \
	languages-site/ 

output = com_altauserpoints.zip

build: $(files)
	zip -r $(output) $^

clean: $(output)
	rm $^