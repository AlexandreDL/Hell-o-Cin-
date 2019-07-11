EQUIPE MARKETING

1 role : super_chef_marketing
2 role : chef_marketing
3 role : employé_marketing

security.yml

 role_hierarchy:
        super_chef_marketing: chef_marketing
        chef_marketing: employé_marketing

EQUIPE DEV

1 role : super_chef_technique
3 role : employé_technique

security.yml

 role_hierarchy:
        super_chef_technique: employé_technique

Donc au final si j'ai un super admin mon security.yml resemblera a celui ci

 role_hierarchy:
        super_chef_technique: employé_technique
        super_chef_marketing: chef_marketing
        chef_marketing: employé_marketing
        super_admin: [super_chef_technique, super_chef_marketing]

