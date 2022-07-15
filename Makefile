release_plugin: dist
	cp -r plugin/ dist/floristdatepicker
	cd dist && zip -r floristdatepicker-latest.zip floristdatepicker/
	rm -fr dist/floristdatepicker

dist: clean
	mkdir dist/

clean:
	rm -fr dist/
