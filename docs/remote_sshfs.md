# Setup a local directory to ssh directory

```bash
# Add a ssh key
utilux-ssh a my-vps.com
ssh-copy-id -i ~/.ssh/my-vps.com.pub user@my-vps.com
sudo mkdir -p /mnt/my-vps.com

# test sshfs connection
sshfs -o IdentityFile=~/.ssh/id_ed25519 ton_user@ip:/home/dist-user /mnt/my-vps.com
# unmount test directory
fusermount -u /mnt/my-vps.com

# Allow other file (useful if a service like docker/php has to read/write from dist directory)
# Do it only once
echo "user_allow_other" | sudo tee -a /etc/fuse.conf

# In /etc/fstab
# Warning: please verify your uid/gid values
# uid=$(id -u)
# gid=$(id -g)
user@my-vps.com:/home/dist-user /mnt/my-vps.com fuse.sshfs _netdev,IdentityFile=/home/local-user/.ssh/my-vps.com,users,idmap=user,allow_other,default_permissions,uid=1000,gid=1000 0 0

# Check fstab syntax file
sudo findmnt --verify

# Manually mount without rebooting (unmount with fusermount -u /mnt/my-vps.com)
# Then auto-mounted on reboot when configured through /etc/fstab
sudo mount /mnt/my-vps.com
```
