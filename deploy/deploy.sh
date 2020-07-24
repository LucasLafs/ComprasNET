#!/bin/bash

sudo apt-key add ./Repo.keys
sudo cp -R ./Sources.list /etc/apt/sources.list
sudo apt-get update
sudo apt-get install dselect
sudo dpkg --set-selections < ./Package.list
sudo dselect
