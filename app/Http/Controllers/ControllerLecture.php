<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class ControllerLecture extends Controller
{
    public function index()
    {
        $final_result = '';
        return view('index', compact('final_result'));
    }

    public function generate(Request $request)
    {
        $csv_file = $request->file('csv_file');

        $filename = $csv_file->getClientOriginalName();
        $extension = $csv_file->getClientOriginalExtension();

        $valid_extension = array("csv");

        if(in_array(strtolower($extension),$valid_extension)){
            $location = 'uploads';
            $csv_file->move($location,$filename);
            $filepath = public_path($location."/".$filename);
            $csv_file = fopen($filepath,"r");
            $importData_arr = array();
            $r = 0;

            while (($filedata = fgetcsv($csv_file, 1000, ",")) !== FALSE) {
                $importData_arr[$r]['lecture'] = $filedata[0];
                if ($filedata[1] == 'lightning') {
                    $importData_arr[$r]['duration'] = 5;
                } else {
                    $importData_arr[$r]['duration'] = (int)$filedata[1];
                }
                $r++;
            }
            fclose($csv_file);

            $hour_ini = date('H:i', strtotime('09:00'));
            $hour_lunch = date('H:i',strtotime('12:00'));
            $hour_end = date('H:i', strtotime('17:00'));
            $final_result = array();
            $count_array = 0;
            $count_track = 0;
            $define_track = 1;
            $duration_previous = 0;
            $hour_save = $hour_ini;

            foreach ($importData_arr as $lectures)
            {
                if ($define_track == 1) {
                    $count_track++;
                    $final_result[$count_track]['title'] = 'Track '.$count_track;
                    $define_track = 0;
                }

                $hour_save = date('H:i', strtotime($hour_save.' + '.$duration_previous.' minutes'));
                if ($count_array == 0) {
                    $final_result[$count_track]['data'][] = $hour_save.'  '.$lectures['lecture'].'  '.$lectures['duration'].' min' ;
                } else {
                    if ($lectures['duration'] > 0) {
                        $hour_next = date('H:i', strtotime($hour_save.' + '.$lectures['duration'].' minutes'));

                        if ($hour_lunch > 0) {
                            if ($hour_save >= $hour_lunch || $hour_next > $hour_lunch) {
                                $final_result[$count_track]['data'][] = $hour_lunch.'  Lunch';
                                $hour_save = date('H:i', strtotime($hour_lunch.' + 1 hour'));
                                $hour_lunch = 0;
                            }
                        }

                        if ($hour_save >= $hour_end || $hour_next > $hour_end) {
                            $final_result[$count_track]['data'][] = $hour_end.'  Networking Event';
                            $count_track++;
                            $final_result[$count_track]['title'] = 'Track '.$count_track;
                            $hour_save = $hour_ini;
                            $hour_lunch = date('H:i',strtotime('12:00'));
                        }

                        $final_result[$count_track]['data'][] = $hour_save.'  '.$lectures['lecture'].'  '.$lectures['duration'].' min';
                    }
                }

                if ($lectures['duration'] > 0) {
                    $duration_previous = $lectures['duration'];
                }
                unset($importData_arr[$count_array]);
                $count_array++;
            }

            if ($hour_save < $hour_end) {
                $final_result[$count_track]['data'][] = $hour_end.'  Networking Event';
            }


            if(File::exists($filepath)){
                File::delete($filepath);
            }

            header('Content-type: text/javascript');
            $final_result = json_encode($final_result,JSON_PRETTY_PRINT);

            flash()->success('Arquivo enviado com sucesso!');
            return view('index', compact('final_result'));

        } else {

            flash()->error('Este tipo de arquivo não é permitido!');
            return redirect('/');
        }
    }
}
