# Public Photo Sharing Application

A cloud-based photo-sharing platform that allows users to upload, share, and manage images securely using AWS services.

## 🚀 Features
- 📸 Upload and share photos publicly
- 🔒 Secure image storage using **Amazon S3**
- 📊 Image metadata management
- 🔍 Search functionality for photos
- 📢 Public sharing links
- 📡 Hosted on **AWS (S3, Lambda, API Gateway, DynamoDB, etc.)**
- 🎨 Responsive UI for mobile and desktop

## 🏗️ Tech Stack
- **Frontend:** React.js / Vue.js / HTML, CSS, JavaScript
- **Backend:** Node.js / Flask / Django
- **Database:** Amazon DynamoDB / PostgreSQL
- **Storage:** AWS S3
- **Authentication:** AWS Cognito / Firebase Auth
- **Hosting:** AWS Amplify / EC2 / S3 + CloudFront

## 🔧 Installation & Setup

### 1️⃣ Clone the Repository
```sh
 git clone https://github.com/yourusername/photo-sharing-app.git
 cd photo-sharing-app
```

### 2️⃣ Install Dependencies
```sh
 npm install  # For Node.js backend
 pip install -r requirements.txt  # For Python backend
```

### 3️⃣ Configure AWS Services
- Create an **S3 Bucket** for image storage
- Set up **AWS Lambda** for backend functions
- Use **DynamoDB** for metadata storage
- Configure **Cognito** for authentication (if applicable)

### 4️⃣ Environment Variables
Create a `.env` file and add:
```sh
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
S3_BUCKET_NAME=your-bucket-name
DYNAMO_DB_TABLE=your-table-name
```

### 5️⃣ Run the Application
```sh
 npm start  # React Frontend
 python app.py  # Flask Backend
```

## 🌍 Deployment
Use **AWS Amplify, EC2, or S3 + CloudFront** for frontend hosting.
```sh
aws s3 sync ./build s3://your-bucket-name --acl public-read
```

For backend, deploy Lambda functions or use EC2:
```sh
serverless deploy
```

## 📜 License
MIT License. Feel free to contribute and modify!

---
Feel free to fork and contribute! 🚀 PRs are welcome! 😊

