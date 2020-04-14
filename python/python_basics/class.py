class fighter ():

    def __init__(self):

        self.health=100
        self.attack=2
        self.heal=1

    def att(self,enemy):
        enemy.health -= self.attack

    def hil(self):
        self.health+=self.heal


noman=fighter()
farhan=fighter()


print(noman.health)
print(farhan.health)

farhan.att(noman)

print(noman.health)
print(farhan.health)

noman.hil()
noman.hil()

print(noman.health)
print(farhan.health)
