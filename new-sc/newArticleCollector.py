import ssl
from collectionSources import *
import MySQLdb
import MySQLdb.cursors

def main():
    ssl._create_default_https_context = ssl._create_unverified_context # monkey patch for getting past SSL errors (this might be a system-specific issue)
    user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/602.2.14 (KHTML, like Gecko) Version/10.0.1 Safari/602.2.14'

    db = MySQLdb.connect(host="127.0.0.1",port=3306,user="root",password="",db="SupremeCourtApp",use_unicode=True,charset="utf8")
    db.autocommit(True)
    c = db.cursor(MySQLdb.cursors.DictCursor)

    try:
        if isNewBillingCycle(c):
            resetRequests(c)
            print("New billing cycle - sentiment requests reset")
            print()
    except MySQLdb.Error as e:
        print("Database error - ",e)
        print("Script aborted.")
        return

    # RSS feeds
    feed_urls = ['https://www.google.com/alerts/feeds/16607645132923191819/10371748129965602805', 'https://www.google.com/alerts/feeds/16607645132923191819/14723000309727640285', 'https://www.google.com/alerts/feeds/16607645132923191819/1276985364450614174', 'https://www.google.com/alerts/feeds/16607645132923191819/1276985364450612172']
    feeds = RSSFeeds(feed_urls)
    feeds.parseFeeds(c)

    # newsAPI results
    queries  = ["USA Supreme Court","US Supreme Court", "United States Supreme Court","SCOTUS"]
    newsapi = NewsAPICollection(queries)
    newsapi.parseResults(c)

    # topic sites
    t = TopicSites()
    t.collect(c)

main()