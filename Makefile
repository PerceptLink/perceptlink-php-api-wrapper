ROOT_DIR=.
TEST_DIR=test
TEMP_DIR=/tmp

.PHONY: clean test

clean:
	rm $(TEMP_DIR)/perceptlink/*

test_deps: clean
	apt-get install -y php5-cli phpunit php5-curl
	mkdir -p $(TEMP_DIR)/perceptlink
	wget http://pear.php.net/go-pear.phar -P $(TEMP_DIR)/perceptlink/
	php $(TEMP_DIR)/perceptlink/go-pear.phar
	pear config-set auto_discover 1
	pear install pear.phpunit.de/PHPUnit

test:
	phpunit $(TEST_DIR)/tests.php
