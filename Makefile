TESTS=$(shell find test -name "*.php")
ST_MAJOR=1
ST_MINOR=1
ST_PATCH=0
SIMPLETEST=$(ST_MAJOR).$(ST_MINOR).$(ST_PATCH)

test: test/support/simpletest .PHONY
	# php ./test/HTTPerfTestSuite.php
	@PATH=$(PWD)/test/support:$(PATH) php ./test/HTTPerfTestSuite.php

test/%: test/support/simpletest .PHONY
	# php $@
	@PATH=$(PWD)/test/support:$(PATH) php $@

test/support/simpletest:
	cd test/support && wget http://hivelocity.dl.sourceforge.net/project/simpletest/simpletest/simpletest_$(ST_MAJOR).$(ST_MINOR)/simpletest_$(SIMPLETEST).tar.gz
	cd test/support && tar -xzf simpletest_$(SIMPLETEST).tar.gz

.PHONY:
