## CMAP Manual QA Checklist (Core Flows)

**Test Environment**: https://staging.curriculum.ok.ubc.ca

Use a single Admin account. Skip auth/roles testing. Use "[QA]" prefix for all test data to keep it separate from production data. Test on the existing dataset first to understand the system, then create new test data.

### Environment & Pre-checks
- [ ] Browser: Latest Chrome/Edge or Firefox
- [ ] Navigate to https://staging.curriculum.ok.ubc.ca
- [ ] Login with provided credentials (email + password + reCAPTCHA)
- [ ] Confirm you reach "My Dashboard" after successful login
- [ ] Check for console errors (F12 Developer Tools) - note any Vue warnings are expected in dev mode

### Test Data (use throughout)
- **Program**: Bachelor of Sleeping
  - Outcomes (PLOs):
    - PLO1 Sleep Hygiene
    - PLO2 Respiratory Wellness
    - PLO3 Circadian Science
    - PLO4 Dream Analysis
    - PLO5 Ethics of Napping
- **Courses**:
  - SLEP 100 Intro to Yawning (3 cr)
  - SLEP 150 Blanket Fort Basics (3 cr)
  - SLEP 201 Intermediate Snoring (3 cr)
  - SLEP 250 Power Napping (2 cr)
  - SLEP 301 Advanced Napping (3 cr)
  - SLEP 399 Sleep Lab I (1 cr)
  - SLEP 499 Capstone in Slumber (4 cr)
- **Prereqs/Coreqs**:
  - SLEP 201 requires SLEP 100
  - SLEP 301 requires SLEP 201; coreq SLEP 399
  - SLEP 250 requires SLEP 100
  - SLEP 499 requires SLEP 301

---

### 1) Programs (Create, Edit, PLOs)
**Navigate**: My Dashboard → Programs section → Click the "+" button (second button, not the help "?" button)

#### 1.1 Create Program
- [ ] Click the "+" button in Programs section header (avoid the "?" help button)
- [ ] Fill "Create a Program" modal:
  - Program Name: `[QA] Bachelor of Sleeping`
  - Campus: Select "Okanagan"
  - Faculty/School: Select "Irving K. Barber Faculty of Science" (dropdown becomes enabled after Campus)
  - Department: Select "Biology" (dropdown becomes enabled after Faculty)
  - Level: Select "Bachelors" radio button
- [ ] Click "Add" button
- [ ] **Expected**: Redirected to Program Wizard Step 1 (Program Learning Outcomes page)
- [ ] **Expected**: Success alert "New program added" appears at top
- [ ] **Expected**: Program header shows name, faculty, department, and level

#### 1.2 Add Program Learning Outcomes (PLOs) - Step 1
**Current Page**: Program Wizard Step 1 - 4-step navigation shows: 1(PLOs), 2(Mapping Scale), 3(Courses), 4(Overview)

- [ ] Click "+ PLO" button in the "Program Learning Outcomes (PLOs)" section
- [ ] In the PLO modal, add each PLO with its short phrase:
  - PLO 1: 
    - Full text: "Demonstrate comprehensive understanding of sleep hygiene principles and their application in daily life"
    - Short Phrase: "Sleep Hygiene"
    - Category: None
  - PLO 2:
    - Full text: "Apply knowledge of respiratory wellness and its impact on sleep quality"
    - Short Phrase: "Respiratory Wellness"
    - Category: None
  - PLO 3:
    - Full text: "Analyze circadian rhythms and their role in optimal sleep patterns"
    - Short Phrase: "Circadian Science"
    - Category: None
  - PLO 4:
    - Full text: "Evaluate dream patterns and interpret their psychological significance"
    - Short Phrase: "Dream Analysis"
    - Category: None
  - PLO 5:
    - Full text: "Apply ethical principles in the context of napping and rest practices"
    - Short Phrase: "Ethics of Napping"
    - Category: None
- [ ] Click "+ Add" button after each PLO entry (it adds to the table within modal)
- [ ] **Expected**: Each PLO appears in the modal's table with edit/delete buttons
- [ ] Click "Save Changes" button in the modal
- [ ] **Expected**: Success alert "Your program learning outcomes were updated successfully!"
- [ ] **Expected**: PLOs now appear in numbered list on main page with drag handles (↕) for reordering
- [ ] **Expected**: Each PLO shows short phrase in bold, full text below, with Edit/Delete buttons

#### 1.3 Edit Program Metadata
- [ ] Click "Edit Program Information" button (at top of wizard)
- [ ] **Test**: Modify program
- [ ] Save and verify changes persist after page refresh
- [ ] Navigate back to "My Dashboard" and verify program shows updated info

#### 1.4 Program Management Actions
- [ ] Test "Duplicate Program" button - verify it creates a copy
- [ ] Test "Add Collaborators" button - verify modal opens and allows email entry
- [ ] Test "Delete Entire Program" button - verify warning appears
- [ ] **Expected**: If program has courses attached, deletion should be blocked or warn about dependencies

---

### 2) Courses (Create, Add CLOs, Link to Programs)
**Navigate**: My Dashboard → Courses section → Click "+" button

#### 2.1 Create New Course
- [ ] Click "+" button in Courses section header
- [ ] Fill "Create a Course" modal:
  - Course Code: `SLEP` (4-letter maximum)
  - Course Number: `100`
  - Course Title: `[QA] Intro to Yawning`
  - Term and Year: Winter Term 2, 2025
  - Course Section: (optional field)
  - Mode of Delivery: Select "Online", "In-person", "Hybrid", or "Multi-Access"
  - Map my course against: Select "Bachelor's degree level standards"
- [ ] Click "Add" button
- [ ] **Expected**: Redirected to Course Wizard Step 1 (Course Learning Outcomes)
- [ ] **Expected**: Success alert "New course added"
- [ ] **Expected**: Course header shows code, title, "No associated programs", and delivery mode

#### 2.2 Add Course Learning Outcomes (CLOs) - Step 1
**Current Page**: Course Wizard Step 1 - 7-step navigation shows: 1(CLOs), 2(Assessment), 3(Teaching/Learning), 4(Alignment), 5(Program Mapping), 6(Standards), 7(Summary)

- [ ] Click "+ CLO" button
- [ ] Add 2-3 CLOs for the course:
  - Example CLO 1: "Identify the physiological mechanisms of yawning"
  - Example CLO 2: "Demonstrate proper yawning techniques in various contexts"
  - Example CLO 3: "Analyze the social implications of contagious yawning"
- [ ] **Expected**: Each CLO appears with edit/delete options
- [ ] **Test editing**: Click Edit on a CLO, modify text, save
- [ ] **Test deleting**: Click Delete on a CLO, confirm removal
- [ ] Navigate through Steps 2-4 (optional - these are Student Assessment Methods, Teaching Activities, Course Alignment)

#### 2.3 Link Course to Program - Step 5
**Navigate**: Course Wizard Step 5 (Program Outcome Mapping) OR from Program Wizard Step 3

**Option A: From Program Wizard**
- [ ] Go to My Dashboard → Click on "[QA] Bachelor of Sleeping" program
- [ ] Navigate to Step 3 (Courses)
- [ ] Click "+ Course From My Dashboard" button
- [ ] In modal, check the course(s) to add (e.g., "[QA] Intro to Yawning")
- [ ] Check the "Required" checkbox if course is required for the program
- [ ] Click "Add Selected"
- [ ] **Expected**: Success message "Successfully added X course(s) to this program"
- [ ] **Expected**: Course appears in table with "Required" checkbox, status shows "Not Mapped"
- [ ] **Expected**: Actions include "Remove", "Add Note", "Go to Course" buttons

**Option B: Create New Course from Program Wizard**
- [ ] In Program Wizard Step 3, click "+ New Course" button
- [ ] Fill in course details in modal
- [ ] Select "Required" or "Not Required" radio button
- [ ] (Optional) Assign Owner For Course - enter email of course owner
- [ ] Click "Add"
- [ ] **Expected**: Course created and automatically linked to program

#### 2.4 Map Course to Program Learning Outcomes
- [ ] From Program Wizard Step 3 courses table, click "Go to Course" link
- [ ] **Expected**: Redirected to Course Wizard Step 5 (Program Outcome Mapping)
- [ ] **Expected**: See collapsible sections for each program the course is linked to
- [ ] Click on program name to expand (e.g., "2. [QA] Bachelor of Sleeping")
- [ ] **Expected**: Mapping grid/matrix showing CLOs vs PLOs with mapping scale options
- [ ] For each CLO, select appropriate mapping level to each PLO:
  - Example mappings (if using I/D/A scale):
    - CLO1 → PLO1 (Sleep Hygiene): Introduced
    - CLO2 → PLO1 (Sleep Hygiene): Developing
    - CLO3 → PLO3 (Circadian Science): Introduced
- [ ] Click "Save" button
- [ ] **Expected**: Success message appears
- [ ] Click "Back to Program" link
- [ ] **Expected**: Course status in Program Wizard changes from "Not Mapped" to "Mapped" (or shows checkmark/percentage)

#### 2.5 Edit Course Information
- [ ] From Course Wizard, click "Edit Course Information" button at top
- [ ] **Test**: Modify course title, term, delivery mode
- [ ] Save and verify changes persist
- [ ] Go back to My Dashboard and verify course shows updated info in courses list

#### 2.6 Course Management Actions
- [ ] Test "Duplicate Course" button - verify it creates a copy
- [ ] Test "Add Collaborators" button - verify modal opens for email entry
- [ ] Test "Delete Course" button - verify warning appears
- [ ] **Expected**: If course is linked to programs, deletion should warn about dependencies

---

### 3) Mapping Scales (Program Wizard Step 2)
**Navigate**: Program Wizard → Step 2 (Mapping Scale)

- [ ] Click "Show Default Mapping Scales" button
- [ ] **Expected**: Modal appears with multiple preset scale options:
  - Mapping Scale 1: Introduced (I), Developing (D), Advanced (A) - 3-point scale
  - Mapping Scale 2: Principal (P), Secondary (S), Major Contributor (Ma), Minor Contributor (Mi)
  - Mapping Scale 3: Yes (Y) - 1-point scale for alignment
  - Mapping Scale 4: Foundations (F), Extensions (E) - 2-point scale
- [ ] Select "Mapping Scale 1" (I/D/A) by clicking "+ Use this scale"
- [ ] **Expected**: Warning that existing scale will be replaced
- [ ] **Expected**: Scale is applied to program; modal closes
- [ ] Click "+ My Own Mapping Scale Level" button
- [ ] **Test**: Create custom mapping level (e.g., "Mastered (M)" with description)
- [ ] **Expected**: Custom scale appears in the list
- [ ] **Test**: Reorder mapping scale levels (if drag-and-drop available)
- [ ] **Test**: Delete a custom mapping level
- [ ] **Note**: Mapping scale is used in Step 5 of Course Wizard to map CLOs to PLOs

---

### 4) Program Overview and Curriculum Map (Program Wizard Step 4)
**Navigate**: Program Wizard → Step 4 (Program Overview)

**Note**: This step becomes meaningful after courses are mapped to PLOs in Course Wizard Step 5

- [ ] Navigate to Program Wizard Step 4
- [ ] **Expected**: See program summary with:
  - Program name, faculty, department, level
  - List of PLOs (5 PLOs: Sleep Hygiene, Respiratory Wellness, Circadian Science, Dream Analysis, Ethics of Napping)
  - List of courses linked to program (Required/Not Required status)
  - Curriculum MAP visualization/matrix showing which courses address which PLOs
- [ ] **Verify coverage**: Each PLO should be addressed by at least one course
  - PLO1 (Sleep Hygiene) → covered by multiple courses at different levels
  - PLO2 (Respiratory Wellness) → covered by at least one course
  - PLO3 (Circadian Science) → covered by at least one course
  - PLO4 (Dream Analysis) → covered by at least one course
  - PLO5 (Ethics of Napping) → covered by at least one course
- [ ] **Check mapping indicators**: 
  - Look for cells/indicators showing I (Introduced), D (Developing), A (Advanced)
  - Color coding or symbols indicating degree of alignment
  - Gaps or warnings for PLOs not covered by any course
- [ ] **Test export/print** (if available):
  - Look for "Export", "Print", "Download" buttons
  - Export to PDF or Excel format
  - **Expected**: File contains complete curriculum map with all PLO-course mappings
- [ ] **Test filtering/sorting** (if available):
  - Filter by required vs non-required courses
  - Sort courses by code, title, or mapping coverage

---

### 5) Dashboard Views and Course Progress
**Navigate**: My Dashboard

#### 5.1 Programs Section
- [ ] Verify "[QA] Bachelor of Sleeping" appears in Programs table
- [ ] **Check columns**:
  - Program name (clickable link to wizard)
  - Faculty and Department/School
  - Level (Bachelors)
  - Courses count (should show number with icon, e.g., " 1" or " 3")
  - Last Updated timestamp (e.g., "2 mins ago")
  - Actions dropdown (three-dot menu)
- [ ] Click on program name → verify redirects to Program Wizard Step 1
- [ ] Click actions menu → test available options (duplicate, delete, etc.)

#### 5.2 Courses Section
- [ ] Verify created courses appear in "My Courses" section
- [ ] **Check columns**:
  - Course Title (clickable link to wizard)
  - Course Code (e.g., "SLEP 100")
  - Term (e.g., "2025 W2")
  - Status: Progress bar and percentage (e.g., "86% complete" or "100% complete")
  - Programs: Count with icon showing how many programs course is linked to
  - Last Updated timestamp
  - Actions dropdown
- [ ] Click "List all Courses" vs "Group by Program" toggle buttons
- [ ] **Expected**: View changes between flat list and grouped by program
- [ ] Click on course title → verify redirects to Course Wizard Step 1
- [ ] Check that course completion percentage updates as wizard steps are completed

#### 5.3 Search and Filters (Dashboard)
- [ ] Look for search/filter options on Programs and Courses tables
- [ ] **Test search** (if available):
  - Search for "[QA]" → should show only QA test items
  - Search for "SLEP" → should show matching course codes
  - Search for "Sleeping" → should show matching program/course titles
- [ ] **Test sorting**: Click column headers to sort by name, code, date, etc.
- [ ] **Expected**: Results update immediately; no page reload

---

### 6) Syllabus Generator (Optional Module)
**Navigate**: My Dashboard → Syllabi section OR Syllabus Generator link in header

**Note**: This is a separate module from the Curriculum MAP - test if time permits

- [ ] Check Syllabi section on dashboard showing linked syllabi for courses
- [ ] **Expected columns**: Course Title, Course Code, Term, Last Updated, Actions
- [ ] Click on a course's syllabus entry
- [ ] **Expected**: Opens syllabus view or editor
- [ ] Test creating a new syllabus:
  - Click "+ " button or "Syllabus Generator" in header
  - Select a course
  - **Expected**: Opens syllabus template with course information pre-filled
- [ ] **Test importing course information** into syllabus (if this option exists)
- [ ] **Test exporting/printing syllabus** to PDF or Word format
- [ ] Verify syllabus shows:
  - Course information (code, title, term, credits)
  - CLOs from the course
  - PLO mappings (if integrated)
  - Assessment methods, schedule, policies

---

### 7) Data Integrity & Validation

#### 7.1 Required Fields
- [ ] Program creation: Try to create program without name → expect error "Program Name is required"
- [ ] Program creation: Try to create without selecting Level → expect error or disabled Add button
- [ ] Course creation: Try to create without Course Code → expect error "Course Code is required"
- [ ] Course creation: Try to create without Course Title → expect error "Course Title is required"
- [ ] PLO creation: Try to add PLO with empty text → expect error or disabled Add button
- [ ] CLO creation: Try to add CLO with empty text → expect error or disabled Add button

#### 7.2 Data Format Validation
- [ ] Course Code: Test 4-letter maximum constraint
  - Enter "SLEEP" (5 letters) → expect validation message or auto-truncate to 4 letters
  - Enter "SLE" (3 letters) → should be accepted
- [ ] Course Number: Test numeric validation
  - Enter "100" → should work
  - Enter "ABC" (non-numeric) → expect error or prevent input
- [ ] Whitespace handling:
  - Enter " SLEP " (with leading/trailing spaces) → expect automatic trimming
  - Enter "  Intro  to  Yawning  " → expect normalized to "Intro to Yawning"

#### 7.3 Duplicate Detection
- [ ] Try to create a second program with same name "[QA] Bachelor of Sleeping"
  - **Expected**: Either prevented, or allowed if different campus/faculty/catalog year
- [ ] Try to create a second course with same code+number+term (e.g., "SLEP 100 2025 W2")
  - **Expected**: Error message about duplicate course or warning prompt
- [ ] Add the same course twice to a program
  - **Expected**: Second addition blocked or ignored with message "Course already in program"

#### 7.4 Special Characters and Unicode
- [ ] Create course with unicode characters: "[QA] Café Nap & Siesta" 
  - **Expected**: Accepted without errors; displays correctly throughout UI
- [ ] Create PLO with special characters and quotes: "Apply "best practices" in sleep hygiene (80% proficiency)"
  - **Expected**: Text saved correctly; quotes/parentheses not escaped
- [ ] Test ampersands, apostrophes, hyphens in names
  - **Expected**: No HTML encoding issues (no &amp; or &#39; displayed)

#### 7.5 Long Text Handling
- [ ] Create PLO with very long text (200+ characters)
  - **Expected**: Either limited by maxlength, or accepted and truncated with ellipsis (...) in list views
- [ ] Create course with very long title (100+ characters)
  - **Expected**: Wraps cleanly in tables; no horizontal scrolling; full text visible on detail page
- [ ] Add very long description/note to course
  - **Expected**: Accepted; shows full text in appropriate sections; doesn't break layout

---

### 8) UI/Interaction Sanity

#### 8.1 Modals and Dialogs
- [ ] Open "Create a Program" modal → verify modal appears centered with backdrop
- [ ] Click backdrop (outside modal) → verify modal does NOT close (or closes if designed to)
- [ ] Click "×" close button → verify modal closes without saving
- [ ] Press ESC key when modal is open → verify modal closes
- [ ] Open modal with form → Tab through fields → verify focus order is logical
- [ ] Submit form with Enter key (if applicable) → verify form submits
- [ ] Test nested modals (e.g., help dialog within create dialog) → verify both can be closed independently

#### 8.2 Success/Error Messages
- [ ] Create a program → verify green success alert "New program added" appears at top
- [ ] Add PLOs → verify success message "Your program learning outcomes were updated successfully!"
- [ ] Try to save incomplete form → verify error messages appear near relevant fields or at top
- [ ] **Check message auto-dismiss**: Success messages should auto-dismiss after 3-5 seconds
- [ ] **Check message dismiss button**: Click × or dismiss button on alert
- [ ] **Check multiple messages**: Trigger multiple actions quickly → verify messages stack or queue properly (not overlapping)

#### 8.3 Navigation and Breadcrumbs
- [ ] Navigate: Dashboard → Program Wizard → Course List
- [ ] Use browser Back button → verify returns to previous page correctly
- [ ] Use "Back to Program" links in Course Wizard → verify returns to correct Program Wizard step
- [ ] Click "My Dashboard" in header → verify always returns to dashboard
- [ ] **Check page titles**: Verify browser tab title updates per page (e.g., "UBC Curriculum MAP")

#### 8.4 Loading States and Performance
- [ ] Trigger save action → look for loading spinner or button disable during save
- [ ] Navigate between wizard steps → verify smooth transitions (no flash of unstyled content)
- [ ] Load dashboard with 10+ programs and courses → verify page loads in <3 seconds
- [ ] Scroll long lists (if >20 items) → verify smooth scrolling, no jank
- [ ] Open large dropdown (faculty/department selectors) → verify responsive

#### 8.5 Responsive Behavior (if applicable)
- [ ] Resize browser window to tablet width (~768px) → verify layout adjusts
- [ ] Resize to mobile width (~375px) → verify critical functions still accessible
- [ ] Test on actual mobile device (if available) → verify touch interactions work
- [ ] Check that modals fit within viewport on small screens

#### 8.6 Keyboard Navigation
- [ ] Navigate forms using Tab key only → verify logical tab order
- [ ] Use Shift+Tab to go backwards → verify reverse order works
- [ ] Use arrow keys in dropdowns → verify selection changes
- [ ] Press Space on checkboxes/radio buttons → verify toggle works
- [ ] Press Enter on buttons → verify action triggers
- [ ] Navigate wizard steps with Tab → verify can reach step numbers and navigate via keyboard

---

### 9) Import/Export Features

#### 9.1 Import PLOs
**Navigate**: Program Wizard Step 1 → Import PLOs section

- [ ] Click link to download "import-plos-template.xlsx"
- [ ] **Expected**: Excel file downloads with columns: Category (optional), PLO Full Text, PLO Short Phrase
- [ ] Fill template with 3 test PLOs
- [ ] Click "Upload" button → select filled template file
- [ ] Click "Import PLOs" button
- [ ] **Expected**: Success message; PLOs appear in list
- [ ] **Test error handling**: Upload template with missing required columns
  - **Expected**: Error message describing which fields are required
- [ ] **Test duplicate handling**: Import PLOs that already exist
  - **Expected**: Either skipped with warning, or appended as new entries

#### 9.2 Import CLOs
**Navigate**: Course Wizard Step 1 → Import CLOs section

- [ ] Download "import-clos-template.xlsx" or "import-clos-template.csv"
- [ ] **Expected**: Template with columns for CLO text
- [ ] Fill with 2-3 test CLOs
- [ ] Upload and import
- [ ] **Expected**: CLOs added to course successfully
- [ ] **Test CSV vs XLSX**: Try both formats → verify both work

#### 9.3 Export/Download Features
- [ ] Look for export/download buttons in:
  - Program Overview (Step 4) - curriculum map export
  - Course Summary (Step 7) - course details export
  - Dashboard - bulk export options
- [ ] **Test export to PDF** (if available):
  - Click PDF export → file downloads
  - Open PDF → verify all content visible, no truncation, proper formatting
- [ ] **Test export to Excel/CSV** (if available):
  - Click Excel export → file downloads
  - Open in Excel → verify columns align, no encoding issues (é, ñ, etc.)
- [ ] **Test "Print" functionality**:
  - Click Print → browser print dialog opens
  - Preview → verify layout suitable for printing, page breaks appropriate

---

### 10) Collaborators and Permissions

#### 10.1 Add Collaborators to Program
**Navigate**: Program Wizard → Click "Add Collaborators" button

- [ ] Click "Add Collaborators" button
- [ ] **Expected**: Modal opens with email input field
- [ ] Enter valid email address (e.g., colleague's email or test email)
- [ ] Select permission level (if options available): Owner, Editor, Viewer
- [ ] Click "Add" or "Invite"
- [ ] **Expected**: Success message; collaborator appears in list
- [ ] **Test invalid email**: Enter "notanemail" → expect validation error
- [ ] **Test self-invitation**: Enter your own email → expect error or warning
- [ ] **Test removal**: Remove a collaborator → verify they're removed from list

#### 10.2 Add Collaborators to Course
**Navigate**: Course Wizard → Click "Add Collaborators" button

- [ ] Follow same steps as program collaborators
- [ ] **Test cross-ownership**: Verify that course owners can map to programs even if not program owner
- [ ] **Expected**: Only course owners/editors see "Map Course" button in Program Wizard

#### 10.3 Assign Course Owner During Creation
**Navigate**: Program Wizard Step 3 → Create New Course → "Assign Owner For Course" field

- [ ] Create new course from program wizard
- [ ] Enter different email in "Assign Owner For Course" field
- [ ] Create course
- [ ] **Expected**: Course is created but owned by specified user (not you)
- [ ] **Expected**: You may not be able to edit the course (depending on permissions model)
- [ ] Leave field blank → **Expected**: You become the owner

---

### 11) Edge Cases and Regression Tests

- [ ] **Duplicate program names**: Create two programs with identical names but different campus/faculty
  - **Expected**: System should allow (programs are unique by combination of attributes)
- [ ] **Duplicate course in different terms**: Create "SLEP 100" for 2025 W2 and 2026 W1
  - **Expected**: Both allowed; treated as different offerings
- [ ] **Remove course from program**: In Program Wizard Step 3, click "Remove" on a mapped course
  - **Expected**: Course unlinked from program; mapping data preserved if course re-added
- [ ] **Delete program with courses**: Try to delete program that has courses linked
  - **Expected**: Warning message; either blocked or cascades unlink
- [ ] **Delete course linked to program**: Try to delete course from Dashboard when it's in a program
  - **Expected**: Warning about program dependencies
- [ ] **Edit course code/number after linking**: Change course from "SLEP 100" to "SLEP 101"
  - **Expected**: References in programs updated automatically, or edit blocked with warning
- [ ] **Browser refresh during form fill**: Fill out "Create Program" form halfway, refresh browser
  - **Expected**: Form data lost (no auto-save), or preserved in localStorage
- [ ] **Concurrent editing**: Open same program in two browser tabs, edit in both
  - **Expected**: Last save wins, or conflict detection/warning
- [ ] **Session timeout**: Leave browser idle for 30+ minutes, try to save
  - **Expected**: Session expired message; redirect to login; data preserved or lost with warning
- [ ] **PLO limit**: Program Wizard Step 1 mentions "20 PLOs per program" limit
  - Try to add 21st PLO → **Expected**: Error or warning about limit
- [ ] **Reorder PLOs**: Use drag handles (↕) to reorder PLOs in Step 1
  - **Expected**: New order persists; click "Save Order" if required
- [ ] **Empty state messages**: View empty dashboard (no programs/courses)
  - **Expected**: Friendly "No programs yet" messages with call-to-action to create first one

---

### Notes for Tester

#### Test Approach
1. **Start fresh**: Use "[QA]" prefix for all test data to keep separate from production
2. **Follow the flow**: 
   - Create program → Add PLOs → Set mapping scale → Create/link courses → Add CLOs → Map CLOs to PLOs → View overview
3. **Test small first**: Create 1 program with 5 PLOs and 3-4 courses before scaling up
4. **Then scale**: Test with 5+ programs, 20+ courses to verify performance and pagination

#### Documentation
- **Screenshot failures**: Capture full browser window showing error state
- **Record environment**: Browser version, OS, screen resolution
- **Reproducible steps**: Write step-by-step instructions to reproduce any bug
- **Expected vs Actual**: Note what you expected to happen vs what actually happened
- **Severity**: Mark as Critical (blocks workflow), High (workaround exists), Medium (cosmetic), Low (minor)

#### Known Behaviors (from testing)
- Vue development warnings in console are expected (not bugs)
- Course code field enforces 4-letter maximum
- "Last Updated" shows relative time (e.g., "2 mins ago", "8 months ago")
- Course completion percentage updates based on wizard steps completed
- Mapping scales from Step 2 determine options available in Course Wizard Step 5
- Help buttons ("?") open guide modals with screenshots and explanations

#### Common Issues to Watch For
- Modal dialogs not closing properly
- Success messages not appearing or not dismissing
- Broken links between programs and courses
- Mapping data lost when course is removed and re-added
- Cascading deletes not working correctly
- Permission issues with collaborators
- Import file parsing errors with special characters
- Export files with formatting/encoding issues


