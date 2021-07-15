import re
import pymysql

db = pymysql.connect(host="localhost",user="root",password="root2021",db="idcard",charset='utf8',port=3306)
cur = db.cursor()

def insert(num,add):
	sql = "insert into fullinfo(numid,address) values('%s', '%s')"%(num,add)
	try:
		cur.execute(sql)
		db.commit()
	except Exception as e:
		print("Insert error:", e)
		db.rollback()
		cur.close()
		
filename = r'/root/id'
f = open (filename, 'r', encoding='gb18030')
line = f.readline()
count = 1
while line:
    try:
        txt = re.split(r' ', line)
        num = str(txt[0])
        add = str(txt[1])
        insert(num, add)
        print("Inserting No.",count," Data")
        line = f.readline()
        count+=1
    except Exception as e:
        print(e)
db.close()
