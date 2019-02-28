wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-6.3.2.deb
sudo dpkg -i elasticsearch-6.3.2.deb
sudo cp /home/vagrant/src/php-persistency/provision-scritps/elasticsearch.yml /etc/elasticsearch/elasticsearch.yml
sudo systemctl enable elasticsearch.service
sudo systemctl start elasticsearch.service

# Browse
#http://192.168.33.10:9200/_cat/health?v