# Kvantum on debian-based distro
# SKIP IF KVANTUM IS ALREADY INSTALLED
sudo add-apt-repository ppa:papirus/papirus
sudo apt update
sudo apt install qt6-style-kvantum qt6-style-kvantum-themes

# Application styles
git clone https://github.com/vinceliuice/WhiteSur-kde.git
cd WhiteSur-kde
chmod +x install.sh
./install.sh --color dark

# Icons theme
git clone https://github.com/vinceliuice/WhiteSur-icon-theme.git
cd WhiteSur-icon-theme
chmod +x install.sh
./install.sh

# Cursors theme
git clone https://github.com/vinceliuice/WhiteSur-cursors.git
cd WhiteSur-cursors
chmod +x install.sh
./install.sh
