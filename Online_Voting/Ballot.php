<?php

session_start();

include 'db_Conn.php';

$error = '';
$success = false;


$has_voted = $_SESSION['has_voted'];

if ($has_voted) {
    echo "You have already voted.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['voter_id'])) {
        $error = "Voter ID not set. Please log in again.";
    }
     else if (!isset($_POST['candidate_id'])) {
        $error = "Please select a candidate.";
    } 

    else{
       
 
  $stmt = $conn->prepare("SELECT has_voted FROM voters WHERE id = ?");
        $stmt->bind_param("i", $voter_id);
        $stmt->execute();
        $stmt->bind_result($has_voted);
        $stmt->fetch();
        $stmt->close();

        if ($has_voted) {
            $error = "You have already cast your vote.";
        } else if (!isset($_POST['candidate_id'])) {
            $error = "Please select a candidate.";
        } else {
            $candidate_id = $_POST['candidate_id'];
            $voter_id = $_SESSION['voter_id'];
        
        $stmt = $conn->prepare("INSERT INTO results (voter_id, candidate_id) VALUES (?, ?)");
        $stmt->bind_param("is", $voter_id, $candidate_id);

        if ($stmt->execute()) {

           $stmt = $conn->prepare("UPDATE voters SET has_voted = 1 WHERE id = ?");
                $stmt->bind_param("i", $voter_id);
                $stmt->execute();
                $stmt->close();
            $success = true;
      
         }
     }
   }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online Voting Ballot</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/Styles/Ballot.css">
</head>
<body>
  <div class="container mt-2">
    <h2 class="text-center mb-4">CAST YOUR VOTE</h2>
    <div id="ballot" class="row"></div>
  </div>
  
  <div class="modal" id="successModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Vote Successful</h5>
        </div>
        <br>
        <div class="modal-body text-center">
          <svg class="tick-mark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="tick-mark__circle" cx="26" cy="26" r="24" fill="none"/>
            <path class="tick-mark__check" fill="none" d="M14 27l7 7 16-16"/>
          </svg>
          <p>Your vote has been successfully cast!</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="redirectToLogin">EXIT</button>
        </div>
      </div>
    </div>
  </div>
   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="assets/js/script.js"></script>
  <script>
    const candidates = [
      { id: 1, name: 'Jeeth', details: 'Party: NOD, Age: 57' },
      { id: 2, name: 'Aneed', details: 'Party: NBR, Age: 45' },
      { id: 3, name: 'Smith', details: 'Party: XCR, Age: 50' },
      { id: 4, name: 'Roye rose', details: 'Party: AJB, Age: 40' },
      { id: 5, name: 'Daya Sri', details: 'Party: GOH, Age: 27' },
      { id: 6, name: 'Alice', details: 'Party: DOM, Age: 50' },
      { id: 7, name: 'Mishra', details: 'Party: EOF, Age: 37' },
      { id: 8, name: 'Aron Raj', details: 'Party: BOJ, Age: 30' },
    ];

    function createCandidateCard(candidate) {
      return `
        <div class="col-xl-6">
          <div class="row justify-content-center">
            <div class="col-xl-11">
              <div class="card pb-4" id="candidate-${candidate.id}" style="position: relative;">
                <div class="card-body" style="font-size:22px">
                  <h5 class="card-title pt-3" style="font-size:32px">${candidate.name} <img src="assets/images/img3.png" height="60px" width="60px" style="border-radius:50px"></h5>
                  <p class="card-text pt-2">${candidate.details}</p>
                  <form method="POST" action="">
                    <input type="hidden" name="candidate_id" value="${candidate.id}">
                    <button type="submit" class="btn btn-primary vote-button pt-1 mr-4 mb-2" style="height:50px;width:100px;font-size:30px">Vote</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    function displayCandidates() {
      const ballotDiv = document.getElementById('ballot');
      ballotDiv.innerHTML = candidates.map(createCandidateCard).join('');
    }
  document.addEventListener('DOMContentLoaded', function() {
      displayCandidates();

      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
          e.preventDefault();

          const candidateId = this.querySelector('input[name="candidate_id"]').value;
          const formData = new FormData(this);

          fetch('', {
            method: 'POST',
            body: formData
          })
          .then(response => response.text())
          .then(data => {
            // Show the success modal
            $('#successModal').modal({ backdrop: 'static', keyboard: false });
            castVote(candidateId);
            setTimeout(() => {
          window.location.href = 'index.php';
        }, 4000);
          })
          .catch(error => {
            console.error('Error:', error);
          });
        });
      });

      document.getElementById('redirectToLogin').addEventListener('click', function() {
        window.location.href = 'index.php'; // Redirect to the login page
      });
    });

    function castVote(candidateId) {
      const card = document.getElementById(`candidate-${candidateId}`);
      if (card) {
        // Highlight the voted card
        card.classList.add('voted-card');

        // Disable the vote button on all cards
        const voteButtons = document.querySelectorAll('.vote-button');
        voteButtons.forEach(button => {
          button.disabled = true;
        });

        // Show the tick mark on the voted card
        card.innerHTML += '<span class="tick-mark">&#10004;</span>';

        // Add animation classes to the SVG elements
        const tickMarkCircle = document.querySelector('.tick-mark__circle');
        const tickMarkCheck = document.querySelector('.tick-mark__check');
        if (tickMarkCircle && tickMarkCheck) {
          tickMarkCircle.classList.add('animated');
          tickMarkCheck.classList.add('animated');
        }
      } else {
        console.error(`Card with ID candidate-${candidateId} not found.`);
      }
    }

   
  </script>
</body>
</html>




