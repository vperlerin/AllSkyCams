#!/usr/bin/python3 

print ("Content-type: text/html\n\n")
import sys
import hashlib, binascii 
import cgi, os
import cgitb; cgitb.enable()

NODE_DATA_DIR = "/usr/sites/allskycams.com/htdocs/cam_data/"

def upload_media():
      print ("TEST")
      device_id = form['device_id']
      mac_addr = form['mac_addr']
      media_type = form['media_type']
      upload_type = form['upload_type']
      media_file = form['media_file']

      if device_id is None or mac_addr is None or media_type is None or upload_type is None :
         res.body = ("FAILED: You are missing fields in the input. Required fields are: device_id, mac_addr, media_type, upload_type")
         return()

      # check to make sure this is an authorized client
      m = hashlib.md5()
      hash_string = str(device_id) + str(mac_addr)
      m.update(hash_string.encode())
      key = m.digest()
      key = binascii.hexlify(key)
      key_file = '/usr/sites/allskycams.com/api_keys/' + key.decode()
      if os.path.isfile(key_file) == 0:
         print ("Authorization Denied.", key_file)
         return()

      print ("MEDIA FILE: ", media_file)
      exit()
      if media_file.filename:
         media_file_raw = media_file.file.read()
      filename = media_file.filename
      el = filename.split("/")
      filename = el[-1]
      print (filename)
      (cam_num, date_str, xyear, xmonth, xday, xhour, xmin, xsec) = parse_date(filename)
      media_date = str(xyear) + "-" + str(xmonth) + "-" + str(xday)
      device_dir = NODE_DATA_DIR + device_id
      media_dir = NODE_DATA_DIR + device_id + "/" + media_date + "/"
      media_dir = NODE_DATA_DIR + device_id + "/" + media_date + "/"
      if os.path.isdir(device_dir) is False:
         os.system("mkdir " + device_dir)
      if os.path.isdir(media_dir) is False:
         os.system("mkdir " + media_dir)
      new_filename = media_dir + filename
      open(new_filename, 'wb').write(media_file_raw)
      print ("done")

form = cgi.FieldStorage(open(sys.stdin.fileno(), 'rb'))
upload_media()
