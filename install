#!/bin/bash

if [ "$EUID" -eq 0 ]
  then echo "Please do not run this script as root"
  exit
fi


home=$(realpath ~)

bashrc_path="$home/.bashrc"

if [ ! -f "$bashrc_path" ]
then
    echo "Creating bashrc file"
    touch $bashrc_path
else
    echo "bashrc exists."
fi

utiluxIsInstalled=$(cat "$bashrc_path" | grep -c -e "utilux")
if [ "$utiluxIsInstalled" = "1" ]
then
    echo "Utilux is already called by your bashrc file"
    exit
fi


echo "Installing utilux in .bashrc"

echo "" >> $bashrc_path
echo ". ~/utilux/.bashrc" >> $bashrc_path
echo "" >> $bashrc_path

echo "utilux installed in your bashrc file"
echo "please relog in your session to fully enable it"