#!/bin/bash
base="/root/results"
www="/var/www/html/results"

create_clear() {
mkdir $1 2> /dev/null
rm $1/* 2> /dev/null
}

create_clear $base
create_clear $www

mkdir implementierung 2> /dev/null
pushd implementierung > /dev/null
results=`find -maxdepth 1 -name ".git"`
if [[ -z $results ]] ; then
 git clone ssh://git@phabricator.v22014112397821332.yourvserver.net/diffusion/I/implementierung.git .
fi

branches=$(git branch -r | sed 's/^.*\*.*$//g' | sed 's/->.*//g')
for branch in $branches; do
	git checkout .
	echo ""
	echo "---"
	git checkout $branch >/dev/null
	echo "---"
	echo ""
	git reset --hard >/dev/null
	branchname=$(echo $branch | sed 's/origin\///g')
	git pull 2> /dev/null
	#echo $res
	if [ "$res" != "Already up-to-date." ]; then
		mkdir build 2> /dev/null
		pushd build > /dev/null
		rm -r * 2> /dev/null
		qmake -Wnone ../cote/cote.pro -r -spec linux-g++ CONFIG+=warn_off

		buildlog=$base/build-$branchname.txt
		date > $buildlog
		time make >/dev/null | tee $buildlog
		cp $buildlog $www/build-$branchname.txt

		pushd test > /dev/null
		testlog=$base/test-$branchname.txt
		date > $testlog
		./test 2>&1 | tee -a $testlog
	
		cp $testlog $www/test-$branchname.txt
		popd >/dev/null #test
		popd >/dev/null #build
	fi
done

