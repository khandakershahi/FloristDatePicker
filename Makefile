release_plugin: dist
	cp -r plugin/ dist/bloomlocal
	cd dist && zip -r bloomlocal-latest.zip bloomlocal/
	rm -fr dist/bloomlocal

dist: clean
	mkdir dist/

clean:
	rm -fr dist/
