<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Classes;
use App\Models\Material;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function __construct()
    {
    }

    public function class(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();
        $classes = $user->classes->filter(function ($class) {
            return $class->status == 'active';
        });

        $classes = $classes->map(function ($class) {
            $teacher = $class->teacher;
            $materials = $class->materials->filter(function ($material) {
                return $material->status == 'active';
            });
            $students = $class->students;
            return [
                'kode' => $class->class_id,
                'name' => $class->name,
                'description' => $class->description,
                'teacherid' => $teacher->user_id,
                'teachername' => $teacher->name,
                'materials' => $materials->map(function ($material) {
                    $quiz = $material->quiz->filter(function ($quiz) {
                        return $quiz->status == 'active';
                    });

                    return [
                        'kode' => $material->material_id,
                        'name' => $material->title,
                        'description' => $material->description,
                        'file' => $material->file,
                        'kode_kelas' => $material->class_id,
                        'quiz' => $quiz->map(function ($quizItem) {
                            return [
                                'kode' => $quizItem->quiz_id,
                                'kode_materi' => $quizItem->material_id, // tambahin kode materi
                                'question' => $quizItem->question,
                                'question_image' => $quizItem->question_image,
                                'type' => $quizItem->type,
                                'choices' => $quizItem->quizChoice->map(function ($choice) {
                                    return [
                                        'kode' => $choice->choice_id, // tambahin kode choice
                                        'kode_quiz' => $choice->quiz_id, // tambahin kode quiz
                                        'order' => $choice->answer_order,
                                        'answer' => $choice->answer,
                                        'answer_image' => $choice->answer_image,
                                    ];
                                })->toArray(),
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
                'students' => $students->map(function ($student) {
                    return [
                        'kode' => $student->user_id,
                        'kode_kelas' => $student->pivot->class_id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'username' => $student->username,
                    ];
                })->toArray(),
            ];
        })->toArray();


        $classes = array_values($classes);
        $classes = array_map(function ($class) {
            $class['students'] = array_values($class['students']);
            $class['materials'] = array_values($class['materials']);
            $class['materials'] = array_map(function ($material) {
                $material['quiz'] = array_values($material['quiz']);
                $material['quiz'] = array_map(function ($quiz) {
                    $quiz['choices'] = array_values($quiz['choices']);
                    return $quiz;
                }, $material['quiz']);
                return $material;
            }, $class['materials']);
            return $class;
        }, $classes);

        return response()->json([
            'status'    => '200',
            'message'   => 'Data berhasil diambil',
            'errors'    => null,
            'data'      => $classes,
        ], 200);
    }

    public function materials(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();
        $classes = $user->classes->filter(function ($class) {
            return $class->status == 'active';
        });

        $materials = $classes->map(function ($class) {
            $materials = $class->materials->filter(function ($material) {
                return $material->status == 'active';
            });
            return $materials->map(function ($material) {
                $quiz = $material->quiz->filter(function ($quiz) {
                    return $quiz->status == 'active';
                });

                return [
                    'kode' => $material->material_id,
                    'name' => $material->title,
                    'description' => $material->description,
                    'file' => $material->file,
                    'kode_kelas' => $material->class_id,
                    'quiz' => $quiz->map(function ($quizItem) {
                        return [
                            'kode' => $quizItem->quiz_id,
                            'kode_materi' => $quizItem->material_id, // tambahin kode materi
                            'question' => $quizItem->question,
                            'question_image' => $quizItem->question_image,
                            'type' => $quizItem->type,
                            'choices' => $quizItem->quizChoice->map(function ($choice) {
                                return [
                                    'kode' => $choice->choice_id, // tambahin kode choice
                                    'kode_quiz' => $choice->quiz_id, // tambahin kode quiz
                                    'order' => $choice->answer_order,
                                    'answer' => $choice->answer,
                                    'answer_image' => $choice->answer_image,
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                ];
            })->toArray();
        })->toArray();

        $materials = array_merge(...$materials);

        return response()->json([
            'status'    => '200',
            'message'   => 'Data berhasil diambil',
            'errors'    => null,
            'data'      => $materials,
        ], 200);
    }

    public function history(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();

        if ($user->role == "teacher") {
            $history = collect();
            $classes = $user->classes->filter(function ($class) {
                return $class->status == 'active';
            });

            foreach ($classes as $class) {
                $materials = $class->materials->filter(function ($material) {
                    return $material->status == 'active';
                });

                foreach ($materials as $material) {
                    $history = $history->concat($material->studentPassMaterialQuiz);
                }
            }
        } else {
            $history = $user->studentPassMaterialQuiz;
        }

        $history = $history->map(function ($item) {
            $jumlah_soal = $item->material->quiz->filter(function ($quiz) {
                return $quiz->status == 'active';
            })->count();

            $answerChoice = $item->studentAnswerChoice
                ->filter(function ($answer) {
                    return $answer->quiz->status == 'active';
                })
                ->map(function ($answer) {
                    return [
                        'kode' => $answer->pass_quiz_id . $answer->quiz_id,
                        'kode_quiz' => $answer->quiz_id,
                        'kode_pass_quiz' => $answer->pass_quiz_id,
                        'type' => $answer->quiz->type,
                        'answer' => $answer->quizChoice->choice_id,
                        'correct_answer' => $answer->quiz->quizChoice
                            ->where('answer_order', $answer->quiz->correct_answer_order)
                            ->first()->choice_id,
                    ];
                })->toArray();

            $answerEssay = $item->studentAnswerEssay
                ->filter(function ($answer) {
                    return $answer->quiz->status == 'active';
                })
                ->map(function ($answer) {
                    return [
                        'kode' => $answer->pass_quiz_id . $answer->quiz_id,
                        'kode_quiz' => $answer->quiz_id,
                        'kode_pass_quiz' => $answer->pass_quiz_id,
                        'type' => $answer->quiz->type,
                        'answer' => $answer->answers,
                        'correct_answer' => "",
                    ];
                })->toArray();



            $score = collect($answerChoice)->filter(function ($answer, $key) {
                return $answer['answer'] == $answer['correct_answer'];
            })->count() . ' / ' . count($answerChoice);

            return [
                'kode' => $item->pass_quiz_id,
                'kode_materi' => $item->material_id,
                'nama_materi' => $item->material->title,
                'kode_kelas' => $item->material->class_id,
                'nama_kelas' => $item->material->class->name,
                'nama_siswa' => $item->student->name,
                'username_siswa' => $item->student->username,
                'tanggal' => $item->created_at->format('d-m-Y H:i:s'),
                'score' => $score,
                'jumlah_soal' => $jumlah_soal . ' soal',
                'jawaban' => array_merge($answerChoice, $answerEssay),
            ];
        })->toArray();

        $history = array_values($history);
        $history = array_map(function ($item) {
            $item['jawaban'] = array_values($item['jawaban']);
            return $item;
        }, $history);

        return response()->json([
            'status'    => '200',
            'message'   => 'Data berhasil diambil',
            'errors'    => null,
            'data'      => $history,
        ], 200);
    }

    public function joinClass(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();

        $validator = Validator::make($request->all(), [
            'kode_kelas' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode kelas tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        $class = Classes::where('class_id', $request->request->get('kode_kelas'))->first();

        if (!$class) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode kelas tidak ditemukan',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $user->classes()->attach($class->class_id);

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil join kelas',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }

    public function createClass(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Nama kelas dan deskripsi kelas tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        if ($user->role != 'teacher') {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $class = Classes::create([
            'class_id' => 'CLS' . rand(100000, 999999),
            'name' => $request->request->get('name'),
            'description' => $request->request->get('description'),
            'teacher_id' => $user->user_id,
        ]);

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil membuat kelas',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }

    public function editClass(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();

        $validator = Validator::make($request->all(), [
            'kode_kelas' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Nama kelas, deskripsi kelas dan kode kelas tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        if ($user->role != 'teacher') {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $class = Classes::where('class_id', $request->request->get('kode_kelas'))->first();

        if (!$class) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode kelas tidak ditemukan',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        if ($class->teacher_id != $user->user_id) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru dari kelas ini',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }
        $class->name = $request->request->get('name');
        $class->description = $request->request->get('description');
        $class->save();

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil mengubah kelas',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }

    public function deleteClass(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();

        $validator = Validator::make($request->all(), [
            'kode_kelas' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode kelas tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        if ($user->role != 'teacher') {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $class = Classes::where('class_id', $request->request->get('kode_kelas'))->first();

        if (!$class) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode kelas tidak ditemukan',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        if ($class->teacher_id != $user->user_id) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru dari kelas ini',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $class->status = 'inactive';
        $class->save();

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil menghapus kelas',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }

    public function createMaterial(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();

        $validator = Validator::make($request->all(), [
            'kode_kelas' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'file' => 'required|mimes:pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Nama materi, deskripsi materi, file materi dan kode kelas tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        if ($user->role != 'teacher') {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $class = Classes::where('class_id', $request->request->get('kode_kelas'))->first();

        if (!$class) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode kelas tidak ditemukan',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        if ($class->teacher_id != $user->user_id) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru dari kelas ini',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $kode = 'MTR' . rand(100000, 999999);
        $filename = $kode . '.' . $request->file('file')->getClientOriginalExtension();
        $request->file('file')->move(app()->basePath('public/pdf'), $filename);

        $material = $class->materials()->create([
            'material_id' => 'MTR' . rand(100000, 999999),
            'title' => $request->request->get('name'),
            'description' => $request->request->get('description'),
            'file' => $filename,
        ]);

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil membuat materi',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }


    public function editMaterial(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();

        $validator = Validator::make($request->all(), [
            'kode_materi' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'file' => 'mimes:pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Nama materi, deskripsi materi, file materi dan kode materi tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        if ($user->role != 'teacher') {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $material = Material::where('material_id', $request->request->get('kode_materi'))->first();

        if (!$material) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode materi tidak ditemukan',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $material->title = $request->request->get('name');
        $material->description = $request->request->get('description');

        if ($request->hasFile('file')) {
            $kode = 'MTR' . rand(100000, 999999);
            $filename = $kode . '.' . $request->file('file')->getClientOriginalExtension();
            $request->file('file')->move(app()->basePath('public/pdf'), $filename);
            $material->file = $filename;
        }

        $material->save();

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil mengubah materi',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }

    public function deleteMaterial(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();

        $validator = Validator::make($request->all(), [
            'kode_materi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode materi tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        if ($user->role != 'teacher') {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $material = Material::where('material_id', $request->request->get('kode_materi'))->first();

        if (!$material) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode materi tidak ditemukan',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $material->status = 'inactive';
        $material->save();

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil menghapus materi',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }

    public function createQuiz(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();

        $validator = Validator::make($request->all(), [
            'kode_materi' => 'required|string',
            'question' => 'required|string',
            'question_image' => 'mimes:jpeg,jpg,png',
            'type' => 'required|string',
            'choices' => 'array',
            'choices.*.answer' => 'required|string',
            'choices.*.answer_image_code' => 'string',
            'files.*.answer_image' => 'mimes:jpeg,jpg,png',
            'choices.*.answer_order' => 'required|integer',
            'correct_answer_order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Pertanyaan, gambar pertanyaan, tipe, pilihan jawaban, gambar pilihan jawaban, urutan jawaban benar dan kode materi tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        if ($user->role != 'teacher') {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $material = Material::where('material_id', $request->request->get('kode_materi'))->first();

        if (!$material) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode materi tidak ditemukan',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $quiz = $material->quiz()->create([
            'quiz_id' => 'QZ' . rand(100000, 999999),
            'question' => $request->request->get('question'),
            'question_image' => '',
            'type' => $request->request->get('type'),
            'correct_answer_order' => $request->request->get('correct_answer_order'),
        ]);

        if ($request->hasFile('question_image')) {
            $kode = 'QZ' . rand(100000, 999999);
            $filename = $kode . '.' . $request->file('question_image')->getClientOriginalExtension();
            $request->file('question_image')->move(app()->basePath('public/image'), $filename);
            $quiz->question_image = $filename;
            $quiz->save();
        }

        if ($request->has('choices')) {
            foreach ($request->request->all()['choices'] as $choice) {
                if (isset($choice['answer_image_code'])) {
                    if ($request->hasFile('files.' . $choice['answer_image_code'] . '.answer_image')) {
                        $kode = 'CH' . rand(100000, 999999);
                        $filename = $kode . '.' . $request->file('files.' . $choice['answer_image_code'] . '.answer_image')->getClientOriginalExtension();
                        $request->file('files.' . $choice['answer_image_code'] . '.answer_image')->move(app()->basePath('public/image'), $filename);
                        $quiz->quizChoice()->create([
                            'choice_id' => 'CH' . rand(100000, 999999),
                            'answer' => $choice['answer'],
                            'answer_image' => $filename,
                            'answer_order' => $choice['answer_order'],
                        ]);
                    }
                } else {
                    $quiz->quizChoice()->create([
                        'choice_id' => 'CH' . rand(100000, 999999),
                        'answer' => $choice['answer'],
                        'answer_image' => '',
                        'answer_order' => $choice['answer_order'],
                    ]);
                }
            }
        }

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil membuat quiz',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }

    public function deleteQuiz(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();
        $validator = Validator::make($request->all(), [
            'kode_quiz' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode quiz tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        if ($user->role != 'teacher') {
            return response()->json([
                'status'    => '400',
                'message'   => 'Anda bukan guru',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $quiz = Quiz::where('quiz_id', $request->request->get('kode_quiz'))->first();

        if (!$quiz) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode quiz tidak ditemukan',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $quiz->status = 'inactive';
        $quiz->save();

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil menghapus quiz',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }

    public function passQuiz(Request $request)
    {
        $userid = $request->request->get('userid');
        $user = User::where('user_id', $userid)->first();
        $validator = Validator::make($request->all(), [
            'kode_materi' => 'required|string',
            'choices' => 'array',
            'choices.*.kode_quiz' => 'required|string',
            'choices.*.kode_choice' => 'required|string',
            'essay' => 'array',
            'essay.*.kode_quiz' => 'required|string',
            'essay.*.answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode materi, jawaban pilihan ganda dan jawaban essay tidak boleh kosong',
                'errors'    => $validator->errors(),
                'data'      => null,
            ], 400);
        }

        $material = Material::where('material_id', $request->request->get('kode_materi'))->first();

        if (!$material) {
            return response()->json([
                'status'    => '400',
                'message'   => 'Kode materi tidak ditemukan',
                'errors'    => null,
                'data'      => null,
            ], 400);
        }

        $passQuiz = $material->studentPassMaterialQuiz()->create([
            'pass_quiz_id' => 'PQ' . rand(100000, 999999),
            'student_id' => $user->user_id,
        ]);

        if ($request->has('choices')) {
            foreach ($request->request->all()['choices'] as $choice) {
                $passQuiz->studentAnswerChoice()->create([
                    'pass_quiz_id' => $passQuiz->pass_quiz_id,
                    'quiz_id' => $choice['kode_quiz'],
                    'choice_id' => $choice['kode_choice'],
                ]);
            }
        }

        if ($request->has('essay')) {
            foreach ($request->request->all()['essay'] as $essay) {
                $passQuiz->studentAnswerEssay()->create([
                    'pass_quiz_id' => $passQuiz->pass_quiz_id,
                    'quiz_id' => $essay['kode_quiz'],
                    'answers' => $essay['answer'],
                ]);
            }
        }

        return response()->json([
            'status'    => '200',
            'message'   => 'Berhasil mengerjakan quiz',
            'errors'    => null,
            'data'      => null,
        ], 200);
    }
}
