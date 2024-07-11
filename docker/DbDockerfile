# Use an official MySQL runtime as a parent image
FROM mysql:latest

# Set the root password for MySQL (change 'password' to your desired password)
ENV MYSQL_ROOT_PASSWORD=password

# Set the default database name that will be created
ENV MYSQL_DATABASE=photo_share_db

# Copy the initialization SQL script into the container (assuming it's in the same directory as this Dockerfile)
COPY init.sql /docker-entrypoint-initdb.d/

# Expose MySQL default port
EXPOSE 3306

# Define default command to run when the container starts
CMD ["mysqld"]
