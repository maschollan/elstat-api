/*

# user
- id
- user_id
- username
- password
- email
- role (teacher/student)
- active (active/inactive)
- timestamp

# class
- id
- class_id
- teacher_id
- name
- description
- timestamp

# class_student
- id
- class_id
- student_id
- timestamp

# materials
- id
- material_id
- class_id
- name
- description
- file
- active (active/inactive)
- timestamp

# quiz
- id
- quiz_id
- material_id
- question
- question_image
- correct_answer_order
- type (choice/essay)
- active (active/inactive)
- timestamp

# quiz_choice
- id
- quiz_id
- choice_id
- answer_order
- answer
- answer_image
- timestamp

# student_pass_material_quiz
- id
- student_id
- material_id
- pass_quiz_id
- score
- timestamp

# student_answer_choice
- id
- pass_quiz_id
- quiz_id
- choice_id
- timestamp

# student_answer_essay
- id
- pass_quiz_id
- quiz_id
- answer
- timestamp


 */
select
    `users`.*,
    `class_students`.`class_id` as `pivot_class_id`,
    `class_students`.`user_id` as `pivot_user_id`
from
    `users`
    inner join `class_students` on `users`.`user_id` = `class_students`.`user_id`
where
    `class_students`.`class_id` = 'CLS1'