# Teacher Access Routes & Features

After registering as a **teacher** and logging in, here are the accessible features:

## 📚 Main Navigation (Bahan / Lessons)
- **Route**: `/lesson` → `lesson.index`
- **View**: `resources/views/lesson/index-teacher.blade.php`
- **Features**:
  - View all lessons
  - Search lessons by title/description
  - Filter by file type
  - Filter by date range
  - Edit lessons
  - Delete lessons
  - Download lessons
  - Preview files

## ➕ Create New Lesson
- **Route**: `/lesson/create` → `lesson.create`
- **View**: `resources/views/lesson/create.blade.php`
- **Features**:
  - Add lesson title
  - Add lesson description
  - Set class/group name
  - Choose visibility (Class Only / Public)
  - Upload file (PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG - Max 10MB)

## 📋 Quiz Management (Kuiz)
- **Route**: `/teacher/quizzes` → `teacher.quizzes.index`
- **Features**:
  - View all created quizzes
  - Create new quiz
  - Edit quizzes
  - Delete quizzes
  - View quiz results/attempts

### Quiz Routes:
- `GET /teacher/quizzes` - List all quizzes
- `GET /teacher/quizzes/create` - Create quiz form
- `POST /teacher/quizzes` - Store quiz
- `GET /teacher/quizzes/{quiz}` - View quiz details
- `GET /teacher/quizzes/{quiz}/edit` - Edit quiz form
- `PUT /teacher/quizzes/{quiz}` - Update quiz
- `DELETE /teacher/quizzes/{quiz}` - Delete quiz

## 👤 Profile Management
- **Route**: `/profile` → `profile.show`
- **Features**:
  - View profile information
  - Edit personal information
  - Change password

### Profile Routes:
- `GET /profile` - View profile
- `GET /profile/edit` - Edit profile form
- `PUT /profile` - Update profile
- `GET /profile/edit-password` - Change password form
- `PUT /profile/password` - Update password

## 💬 Forum Access
- **Route**: `/forum` → `forum.index`
- **Features**:
  - View forum topics
  - Create forum topics
  - Edit own topics
  - Delete own topics
  - Reply to topics

## 🏠 Home Page Redirect
- **Route**: `/` → automatically redirects to `/lesson` for teachers
- **Route**: `/` → automatically redirects to `/performance` for students

## Login Flow
1. Teacher registers with role = "teacher"
2. Redirected to `/login` page
3. Login with credentials
4. Automatically redirected to `/lesson` (teacher dashboard)
5. Can access all teacher features from navigation menu
