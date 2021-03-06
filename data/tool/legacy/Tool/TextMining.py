from ToolClass import Twitter,TwitterList
from pymongo import MongoClient

twitters = TwitterList()
twitters.calculateTF()
twitters.calculateTFIDF()

client = MongoClient()
db = client.test
Domain = db.Domain
cursor = Domain.find()
# Calculate importance to each domain 
for document in cursor:
    twitters.getCorrelation(document['domain'], document['keywords'])# Calculate importance of each domain by TFIDF
res = twitters.updateToDatabase()
if res:
    print 'finished'