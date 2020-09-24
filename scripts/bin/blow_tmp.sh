# !/bin/sh
 
# delete files older than x min (-amin)
#find /tmp -maxdepth 1 -type f -name \*.jpg -amin +240 -delete
#find /tmp -maxdepth 1 -type f -name sess_\* -atime +21 -delete
find /tmp -maxdepth 1 -type f -mtime +1 -exec rm {} \;
find /var/tmp -maxdepth 1 -type f -amin +240 -delete
find /home/www/tmpbilder/ -type f -amin +240 -delete
find /home/www/tmpbilder/ -type l -amin +240 -delete

# only delete files if directory exists
if [ -d /home/www/counter/albumtmp ]; then
   find /home/www/counter/albumtmp -maxdepth 1 -type f -amin +180 -delete
   # if the directory is empty delete it
   if [ ! -z `find /home/www/counter/albumtmp -empty -type d` ]; then
      rmdir /home/www/counter/albumtmp
   fi
fi
