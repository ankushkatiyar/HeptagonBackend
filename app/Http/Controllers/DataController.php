<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DataController extends Controller
{
  public function get()
  {
    $data = [
      '0' => [
        'title' => 'Test',
        'Desc'  => [
          '0' => 'desc1',
          '1' => 'desc2',
        ]
      ],
      '1' => [
        'title' => 'Test1',
        'Desc'  => [
          '0' => 'desc11',
          '1' => 'desc22',
        ]
      ]
    ];
    return $data;
  }

  public function filter($param = '')
  {
    $data_source = '[
      {
        "id": 1,
        "title": "Antwoord op vragen van de leden Kröger en Klaver over het COVID-19 virus",
        "description": {
          "desc1": "...Dit is een algemene richtlijn, vandaar dat er voor de omgang met corona een specifieke richtlijn is in de nabijheid van een corona- tiënt",
          "desc2": "Vraag 11 Wat is de marktprijs voor het inkopen van een FFP1 mondkapje, hoeveel mondkapjes hiervan zijn,de oproep van de burgemeester van Rotterdam dat er geen",
          "desc3": "Lorem Ipsum is"
        },
        "date": "2021-06-01",
        "score": "0.3",
        "matched": []
      },
      {
        "id": 2,
        "title": "Lorem custom 2",
        "description": {
          "desc1": "Lorem Ipsum is simply dummy demo text of the printing and typesetting industry",
          "desc2": "Lorem Ipsum is simply dummy data",
          "desc3": "Lorem Ipsum"
        },
        "date": "2021-06-02",
        "score": "1.3",
        "matched": []
      },
      {
        "id": 3,
        "title": "Lorem title 3",
        "description": {
          "desc1": "Contrary to popular belief, Lorem Ipsum is not simply random text",
          "desc2": "Contrary to popular to",
          "desc3": "Lorem Ipsum simply"
        },
        "date": "2021-06-03",
        "score": "5.3",
        "matched": []
      }
    ]';

    $data_source_in_array = json_decode($data_source, true);

    $param_array = explode(" ", $param);

    $filtered_data = array();
    foreach ($data_source_in_array as $key => $inner_data) {
      $description = $inner_data['description'];
      $title = $inner_data['title'];
      $found_in_desc = false;
      $found_data = array();
      if (is_array($description) && !empty($description)) {
        foreach ($description as $k => $desc) {
          foreach ($param_array as $param_str) {
            if (strpos(strtolower($desc), strtolower($param_str)) !== false) {
              $found_data["description"][$k] = $desc;
              $found_in_desc = true;
              continue;
            }
          }
        }
      }

      if ($found_in_desc) {
        $found_data["title"] = $title;
      } else {
        foreach ($param_array as $param_str) {
          if (strpos(strtolower($title), strtolower($param_str)) !== false) {
            $found_data["title"] = $title;
            $found_data["description"] = "";
            break;
          }
        }
      }
      if (!empty($found_data)) {
        $filtered_data[] = $found_data;
      }
    }

    return response()->json([
      'success' => true,
      'message' => '',
      'data' => $filtered_data
    ], 200);
  }
}
