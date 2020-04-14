main: main.exe receive.exe
    g++ -o main main.exe extFunc.exe
main.exe: main.cpp extFunc.h
    g++ -c main.cpp
receive.exe: extFunc.h extFunc.cpp
    g++ -c extFunc.cpp
run:
    main
clean:
    rm *. main
