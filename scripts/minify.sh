#!/bin/bash

currDir=$(pwd)
scriptDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null && pwd)"

cd "$scriptDir" || exit

help="Usage:
    minify.sh [options]
Options:
    -i    Input JS file
    -o    Output JS file
    -h    Help Message"

while getopts ":i:o:h" optName
do
    case "$optName" in
        i) inputFilePath="$OPTARG";;

        o) outputFilePath="$OPTARG";;

        h) echo "$help";
           exit;
           break;;

        :) echo "Missing argument for -$OPTARG";
           exit;
           break;;

        *) echo "Invalid option. Try \`minify.sh -h\` for more information";
           exit;
           break;;
    esac
done

if [ -z ${inputFilePath+x} ]; then
    echo "Input File Path Is Not Set. Try \`minify.sh -h\` for more information";
    exit;
fi

if [ -z ${outputFilePath+x} ]; then
    echo "Output File Name Is Not Set. Try \`minify.sh -h\` for more information";
    exit;
fi

cd "$scriptDir/../../../public/assets/js" || exit

uglifyjs "$inputFilePath" -o "$outputFilePath"

cd "$currDir" || exit

exit