#!/bin/sh 

#Limpando processos presos
for i in `ps aux| grep timeout | awk '{print$2}' `; do kill -9 $i ; done 