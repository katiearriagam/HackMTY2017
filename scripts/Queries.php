<?php
	function GetUserRoles($conn, $userID){
		$query = "SELECT r.Name, r.Hex FROM Role r, Users u, UserRole ur WHERE ur.UserID = $userID AND ur.RoleID = r.ID AND ur.UserID = u.ID";

		$result = $conn->query($query);
		return $result;
	}

	function GetUserID($conn, $username){
		$query = "SELECT u.ID FROM Users u WHERE u.username = $username";

		$result = $conn->query($query);
		$row = $result->fetch_assoc();
		return $row['ID'];
	}

	function GetUserSkills($conn, $userID){
		$query = "SELECT s.Name, s.Hex FROM Skill s, Users u, UserSkill us WHERE us.UserID = $userID AND us.SkillID = s.ID AND us.UserID = u.ID";

		$result = $conn->query($query);
		return $result;
	}

	function GetUserInfo($conn, $userID){
		$query = "SELECT * FROM Users WHERE ID = $userID";

		$result = $conn->query($query);
		return $result;
	}

	// Projects with the given status that the user is enrolled in
	function GetUserProjects($conn, $userID, $projectStatus){
		$query = "SELECT p.ID FROM Project p, Users u, Enroll e WHERE e.UserID = $userID AND e.ProjectID = p.ID AND e.UserID = u.ID AND p.Status = $projectStatus";

		$result = $conn->query($query);
		return $result;
	}

	function GetUserProjects($conn, $projectStatus){
		$query = "SELECT p.ID FROM Project p WHERE p.Status = $projectStatus";

		$result = $conn->query($query);
		return $result;
	}

	function GetProjectInfo($conn, $projectID){
		$query = "SELECT * FROM Project WHERE ID = $projectID";

		$result = $conn->query($query);
		return $result;
	}

	// Users enrolled in a given project
	function GetEnrolledUsers($conn, $projectID){
		$query = "SELECT u.ID FROM Enroll e, Project p, Users u WHERE e.ProjectID = $projectID AND e.userID = u.ID AND e.ProjectID =  p.ID";

		$result = $conn->query($query);
		return $result;
	}

	function GetProjectSkills($conn, $projectID){
		$query = "SELECT s.ID FROM Skill s, Project p, ProjectSkill ps WHERE ps.projectID = $projectID AND ps.projectID = p.ID AND ps.skillID = s.ID";

		$result = $conn->query($query);
		return $result;
	}

	function GetProjectRoles($conn, $projectID){
		$query = "SELECT r.ID FROM Role r, Project p, ProjectRole pr WHERE pr.projectID = $projectID AND pr.projectID = p.ID AND pr.roleID = r.ID";

		$result = $conn->query($query);
		return $result;
	}

	function GetRoleInfo($conn, $roleID){
		$query = "SELECT * FROM Role WHERE ID = $roleID";

		$result = $conn->query($query);
		return $result;
	}

	function GetSkillInfo($conn, $skillID){
		$query = "SELECT * FROM Skill WHERE ID = $skillID";

		$result = $conn->query($query);
		return $result;
	}

	// Enroll a user in a project and delete the request. (Accept request)
	function EnrollProject($conn, $projectID, $userID){
		$insert = "INSERT INTO Enroll VALUES ($projectID, $userID)";
		$delete = "DELETE FROM Request WHERE ProjectID = $projectID AND UserID = $userID";

		$inserted = $conn->query($insert);
		$deleted = $conn->query($delete);
		return $inserted and $deleted;
	}

	// Projects owned by a given user
	function GetOwnedProjects($conn, $userID){
		$query = "SELECT p.ID FROM Project p, Users u WHERE p.OwnerUser = $userID AND p.OwnerUser = u.ID;";

		$results = $conn->query($query);
		return $results;
	}

	function GetProjectOwner($conn, $projectID){
		$query = "SELECT p.OwnerUser FROM Project p WHERE p.ID = $projectID";

		$result = $conn->query($query);
		return result;
	}

	// Requests for a given project
	function GetProjectRequests($conn, $projectID){
		$query = "SELECT * FROM Request WHERE ProjectID = $projectID";

		$results = $conn->query($query);
		return $results;
	}

	function DeleteRequest($conn, $projectID, $userID){
		$query = "DELETE FROM Request WHERE ProjectID = $projectID AND UserID = $userID";

		$result = $conn->query($query);
		return $result;
	}

	function CreateProject($status, $name, $desc, $shortDesc, $photo, $ownerID){
		$query = "INSERT INTO Project VALUES(NULL, $status, $name, $desc, $shortDesc, $photo, $ownerID)";

		$result = $conn->query($query);
		return $result;
	}

	function CreateRequest($conn, $userID, $projectID){
		$query = "INSERT INTO Request VALUES($projectID, $userID, NOW())";

		$result = $conn->query($query);
		return $result;
	}

	function JoinLeavePending($conn, $userID, $projectID){
		$joinedQuery = "SELECT COUNT(*) FROM Enroll WHERE ProjectID = $projectID && UserID = $userID";

		$requestedQuery = "SELECT COUNT(*) FROM Request WHERE ProjectID = $projectID && UserID = $userID";

		if ($requestedQuery > 0){
			return "Pending";
		}

		if ($joinedQuery > 0){
			return "Leave";
		}
		else{
			return "Join";
		}
	}

	function SearchUsers($conn, $role, $skill){
		$roleFilter = !($role == null || $role == "");
		$skillFilter = !($skill == null || $skill == "");

		if (!$roleFilter && !$skillFilter){
			$query = "SELECT u.ID FROM Users u";
		}
		else{
			if (!$roleFilter && $skillFilter){
				$query = "SELECT u.ID FROM Users u, UserSkill us, Skill s WHERE us.userID = u.ID AND us.SkillID = s.ID AND s.Name = $skill";
			}
			else if ($roleFilter && !$skillFilter){
				$query = "SELECT u.ID FROM Users u, UserRole ur, Role r WHERE ur.userID = u.ID AND ur.RoleID = r.ID AND r.Name = $role";
			}
			else{
				$query = "SELECT DISTINCT u.ID FROM Users u, UserRole ur, Role r, UserSkill us, Skill s WHERE (ur.userID = u.ID AND ur.RoleID = r.ID AND r.Name = $role) OR (us.userID = u.ID AND us.SkillID = s.ID AND s.Name = $skill)";
			}
		}

		$results = $conn->query($query);
		return $results;
	}

?>