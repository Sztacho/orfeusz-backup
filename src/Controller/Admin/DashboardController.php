<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Crud\UserCrud;
use App\Entity\Anime;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Image;
use App\Entity\LiveChatConnection;
use App\Entity\Season;
use App\Entity\Studio;
use App\Entity\Tag;
use App\Entity\TagType;
use App\Entity\User;
use App\Entity\VideoPlayer;
use App\Entity\Webhook;
use App\Repository\AnimeRepository;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\EpisodeRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{

    public function __construct(
        private readonly UserRepository    $userRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly AnimeRepository   $animeRepository,
        private readonly EpisodeRepository $episodeRepository,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly CommentRepository $commentRepository
    )
    {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $this->userRepository->count([]),
            'episodes' => $this->episodeRepository->count([]),
            'anime' => $this->animeRepository->count([]),
            'article' => $this->articleRepository->count([]),
            'episodeTranslatedAtThisMonth' => $this->episodeRepository->countTranslatedEpisodesInCurrentMonth(),
            'commentToCheck' => $this->commentRepository->count(['isActive' => false]),
        ]);
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addWebpackEncoreEntry('dashboard');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->generateRelativeUrls()
            ->setTitle('OrfeuszApi');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userData = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);
        $userMenu = parent::configureUserMenu($user);
        $userMenu->addMenuItems([
            MenuItem::linkToUrl('My Profile', 'fa fa-id-card', $this->adminUrlGenerator
                ->setController(UserCrud::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($userData->getId())
                ->generateUrl())
        ]);

        if ($userData->getDiscordId()) {
            $userMenu->setAvatarUrl('https://cdn.discordapp.com/avatars/'.$userData->getDiscordId().'/'.$userData->getAvatar());
        }

        return $userMenu;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Anime');
        yield MenuItem::linkToCrud('Anime List', 'fas fa-video', Anime::class);
        yield MenuItem::linkToCrud('Episode List', 'fas fa-film', Episode::class);
        yield MenuItem::linkToCrud('Video Player List', 'fas fa-play', VideoPlayer::class);
        yield MenuItem::section('Anime Configuration');
        yield MenuItem::linkToCrud('Season List', 'fas fa-camera', Season::class);
        yield MenuItem::linkToCrud('Studio List', 'fas fa-building-columns', Studio::class);
        yield MenuItem::linkToCrud('Tag List', 'fas fa-tags', Tag::class);
        yield MenuItem::linkToCrud('Tag Type List', 'fas fa-tag', TagType::class);
        yield MenuItem::section('Moderation');
        yield MenuItem::linkToCrud('Article List', 'fas fa-list', Article::class);
        yield MenuItem::linkToCrud('Comment List', 'fas fa-comment', Comment::class);
        yield MenuItem::linkToCrud('Live Chat List', 'fas fa-network-wired', LiveChatConnection::class);
        yield MenuItem::section('Other');
        yield MenuItem::linkToCrud('Image List', 'fas fa-image', Image::class);
        yield MenuItem::section('Settings');
        yield MenuItem::linkToCrud('User List', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Webhook List', 'far fa-circle fa-fade', Webhook::class);
    }
}
