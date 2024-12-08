# **HealthTech Application**

## **Overview**
The HealthTech Application is a web-based platform designed to streamline patient management and appointment scheduling for healthcare providers. Built with **Laravel**, it includes features for patient registration, doctor management, and appointment booking. This application leverages modern cloud tools and best practices to ensure scalability, responsiveness, and security.

---

## **Features**
### Core Features:
- **Register as a Patient**: Patients can create profiles by signing up.
- **Book Appointments**: Patients can book appointments with available doctors.
- **Admin Panel**: Manage appointments, confirm or cancel bookings, and track performance.
- **SMS Notifications**: Patients are notified via SMS upon booking confirmation.
- **File Upload**: Patients can upload and manage medical documents securely using Appwrite storage.
- **Performance Monitoring**: Integrated with Sentry to track application health and performance.
- **Responsive Design**: The app works seamlessly across all devices.

### **Cloud Integration**
- **AWS S3**: Used for storing and retrieving uploaded files securely.
- **AWS SNS**: Enables SMS notifications for appointment confirmations.
- **Appwrite Storage**: An alternative file storage solution with secure APIs.
- **AWS Elastic Beanstalk**: Deployment platform for scaling and running the application.

---

## **Technologies Used**
### **Backend**
- **Laravel 11**: PHP framework for rapid application development.
- **MySQL (AWS RDS)**: Relational database for managing user and application data.
- **PHPUnit**: Testing framework for backend functionality.

### **Frontend**
- **Blade Templates**: Lightweight templating engine for dynamic views.
- **TailwindCSS**: Modern CSS framework for responsive and customizable designs.

### **Cloud Services**
- **AWS S3**: Secure object storage for user-uploaded files.
- **AWS SNS**: Notification system for sending SMS confirmations.
- **AWS Elastic Beanstalk**: Deployment and scaling of the application.
- **Appwrite**: Alternative file storage and management system.

### **Monitoring and Analytics**
- **Sentry**: Real-time performance monitoring and error tracking.

### **Deployment**
- **GitHub Actions**: Continuous integration and deployment pipeline.
- **AWS CLI**: Command-line interface for managing AWS resources.

---

## **Setup Instructions**
### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL
- Node.js and npm
- AWS CLI (configured)
- Appwrite (optional for storage)

### Installation Steps
1. **Clone the Repository**:
   ```bash
   git clone <repository-URL>
   cd healthtech-application
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**:
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Update the `.env` file with your database and AWS credentials:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=healthtech
     DB_USERNAME=root
     DB_PASSWORD=your_password

     AWS_ACCESS_KEY_ID=your_aws_access_key
     AWS_SECRET_ACCESS_KEY=your_aws_secret_key
     AWS_DEFAULT_REGION=us-east-1
     AWS_BUCKET=healthtech-app
     ```

4. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

5. **Seed the Database**:
   ```bash
   php artisan db:seed
   ```

6. **Run the Development Server**:
   ```bash
   php artisan serve
   ```

7. **Set Up AWS Resources**:
   - **S3 Bucket**:
     - Create an S3 bucket for file storage:
       ```bash
       aws s3 mb s3://healthtech-app --region us-east-1
       ```
   - **SNS Notifications**:
     - Set up an SNS topic for SMS notifications in the AWS Management Console.
     - Add the ARN of the topic to the `.env` file:
       ```env
       AWS_SNS_TOPIC_ARN=arn:aws:sns:us-east-1:123456789012:HealthTechAppTopic
       ```

8. **Access the Application**:
   Open your browser and navigate to `http://127.0.0.1:8000`.

---

## **AWS Configuration**
### S3 Integration
- Ensure your AWS CLI is configured with valid credentials:
  ```bash
  aws configure
  ```
- Use S3 for storing uploaded files by configuring Laravel’s `filesystems.php`:
  ```php
  's3' => [
      'driver' => 's3',
      'key' => env('AWS_ACCESS_KEY_ID'),
      'secret' => env('AWS_SECRET_ACCESS_KEY'),
      'region' => env('AWS_DEFAULT_REGION'),
      'bucket' => env('AWS_BUCKET'),
  ],
  ```

### SNS Integration
- Create an SNS topic in AWS:
  - Go to the [SNS Console](https://console.aws.amazon.com/sns/).
  - Create a new topic (e.g., `HealthTechAppTopic`).
  - Copy the topic ARN to your `.env` file.

---

## **Folder Structure**
- `/app`: Application logic (controllers, models, middleware)
- `/resources`: Views and frontend assets
- `/routes`: Web routes configuration
- `/database`: Migrations and seeders
- `/public`: Public assets (CSS, JS, images)

---

## **How to Contribute**
1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add feature: <feature-name>"
   ```
4. Push to your branch:
   ```bash
   git push origin feature-name
   ```
5. Open a Pull Request on GitHub.

---

## **License**
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
