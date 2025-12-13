@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Create Lesson</div>
                <div class="sub">Add a new lesson material for your class</div>
            </div>
            <a href="{{ route('lesson.index') }}" class="btn btn-secondary">
                ← Back to Lessons
            </a>
        </div>

        <section class="panel panel-spaced" style="margin-top: 60px; max-width: 600px;">
            <div class="panel-header">Create New Lesson</div>

            <form method="POST" action="{{ route('lesson.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Lesson Title -->
                <div class="form-group">
                    <label for="title">Lesson Title</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-input"
                        required 
                        placeholder="e.g. Introduction to Variables"
                        value="{{ old('title') }}"
                    >
                    @error('title')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        placeholder="Brief description of the lesson..."
                        style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none; transition: box-shadow 0.12s ease, border-color 0.12s ease;"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Class Group -->
                <div class="form-group">
                    <label for="class_group">Class/Group</label>
                    <input 
                        type="text" 
                        id="class_group" 
                        name="class_group" 
                        class="form-input"
                        required 
                        placeholder="e.g. Form 1 Science"
                        value="{{ old('class_group') }}"
                    >
                    @error('class_group')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Visibility -->
                <div class="form-group">
                    <label for="visibility">Visibility</label>
                    <select 
                        id="visibility" 
                        name="visibility" 
                        required
                        style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                    >
                        <option value="">-- Select Visibility --</option>
                        <option value="class" {{ old('visibility') == 'class' ? 'selected' : '' }}>Class Only</option>
                        <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Public</option>
                    </select>
                    @error('visibility')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <!-- File Upload -->
                <div class="form-group">
                    <label for="file">Upload File (Optional)</label>
                    <input 
                        type="file" 
                        id="file" 
                        name="file" 
                        accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png"
                        style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                    >
                    <small style="display: block; margin-top: 6px; color: var(--muted);">Supported: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG (Max 10MB)</small>
                    @error('file')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">
                        ✨ Create Lesson
                    </button>
                    <a href="{{ route('lesson.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </main>
</div>
@endsection
