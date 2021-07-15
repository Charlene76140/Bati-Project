<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Task;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        for($i = 1; $i < 4; $i++){
            $user = new User();
            $user->setEmail("mail" . $i . "@gmail.com");
            $password = $this->encoder->encodePassword($user, "password" . $i);
            $user->setPassword($password);
            $user->setFirstname("Firstname " . $i);
            $user->setLastname("Lastname " . $i);
            $user->setRoles([]);

            for($j = 0; $j < mt_rand(1,6); $j++){
                $project = new Project();
                $project->setName("Projet n° " . $j);
                $project->setDescription("E imperasset interitu trabeae quadriennio quoque et vicensimo vicensimo vita quos Galla quoque.");
                $project->setCreationDate(new \DateTime());
                $int= mt_rand(1608043260,1671115260);
                $date= date("Y-m-d", $int);
                $project->setDeadline(new \DateTime($date));
                $project->setUser($user);

                $manager->persist($project);

                for($k = 0; $k < mt_rand(0, 8); $k++){
                    $task = new Task();
                    $task->setName("Tache n° " . $k);
                    $task->setDescription("inopia pauloque tempore restituit etiam ex lassatis.");
                    $task->setCreationDate(new \DateTime());

                    $int= mt_rand(1262055681,1262055681);
                    $date= date("Y-m-d", $int);
                    $task->setDeadline(new \DateTime($date));

                    $task->setProject($project);
                    $status= mt_rand(1,2);
                    if($status == 1){
                        $task->setStatus("En cours");
                    }
                    else{
                        $task->setStatus("Terminé");
                    }
                    $manager->persist($task);
                }
            }
            $manager->persist($user);
        }
        $manager->flush();
    }
}
